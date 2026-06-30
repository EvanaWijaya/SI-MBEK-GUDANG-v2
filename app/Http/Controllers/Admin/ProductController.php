<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\Product;
use App\Models\Media;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display product list
     */
    public function index()
    {
        $products = Product::with(['formula', 'primaryImage'])
            ->latest()
            ->get();

        return view('admin.product.index', compact('products'));
    }

    public function generateCode($category)
    {
        return response()->json([
            'code' => Product::generateProductCode($category)
        ]);
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $formulas = Formula::where('is_active', true)
            ->orderBy('formula_name')
            ->get();

        $productCode = Product::generateProductCode('feed');

        return view('admin.product.create', compact(
            'formulas',
            'productCode'
        ));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $request->merge([
            'selling_price' => $request->selling_price
                ? preg_replace('/[^0-9]/', '', $request->selling_price)
                : null
        ]);

        $request->validate(
            [
                'product_name' => 'required|string|max:255',
                'selling_price' => 'nullable|numeric|min:0',
                'reorder_point' => 'nullable|integer|min:0',
                'formula_id' => 'nullable|exists:formulas,id',
                'category' => 'required|in:feed,medicine',
                'source' => 'required|in:production,purchase',
                'description' => 'nullable|string',
                'images' => 'nullable|array|max:10',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ],

            [
                'images.*.max' => 'Ukuran gambar maksimal 2 MB.',
            ]
        );

        if ($request->source === 'production' && !$request->formula_id) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'formula_id' => 'Produk produksi wajib memiliki formula.'
                ]);
        }

        DB::transaction(function () use ($request) {

            $productCode = Product::generateProductCode($request->category);

            $product = Product::create([
                'product_code' => $productCode,
                'product_name' => $request->product_name,
                'description' => $request->description,
                'selling_price' => $request->selling_price,
                'stock' => 0,
                'reorder_point' => $request->reorder_point ?? 0,
                'formula_id' => $request->formula_id,
                'category' => $request->category,
                'source' => $request->source,
                'created_by' => auth('admin')->id(),
            ]);

            $this->logActivity('product_created', $product);

            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $index => $file) {

                    $path = $file->store('products', 'public');

                    $product->media()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'type' => 'image',
                        'is_primary' => $index === 0,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display product details
     */
    public function show(Product $product)
    {
        $product->load([
            'formula',
            'media',
            'primaryImage',
            'stocks',
        ]);

        $formulas = Formula::where('is_active', true)->get();

        return view('admin.product.show', compact(
            'product',
            'formulas'
        ));
    }

    /**
     * Update product data
     */
    public function update(Request $request, Product $product)
    {
        $request->merge([
            'selling_price' => $request->selling_price
                ? preg_replace('/[^0-9]/', '', $request->selling_price)
                : null
        ]);

        $request->validate(
            [
                'product_name' => 'required|string|max:255',
                'selling_price' => 'nullable|numeric|min:0',
                'reorder_point' => 'nullable|integer|min:0',
                'formula_id' => 'nullable|exists:formulas,id',
                'category' => 'required|in:feed,medicine',
                'source' => 'required|in:production,purchase',
                'description' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ],

            [
                'images.*.max' => 'Ukuran gambar maksimal 2 MB.',
            ]
        );

        if ($request->source === 'production' && !$request->formula_id) {
            return back()
                ->withInput()
                ->withErrors([
                    'formula_id' => 'Produk produksi wajib memiliki formula.'
                ]);
        }

        // Validasi maksimal total 10 gambar
        $currentImages = $product->media()->count();
        $newImages = count($request->file('images', []));

        if (($currentImages + $newImages) > 10) {
            return back()
                ->withInput()
                ->withErrors([
                    'images' => 'Total gambar maksimal 10 file.'
                ]);
        }

        DB::transaction(function () use ($request, $product) {

            $product->update([
                'product_name' => $request->product_name,
                'description' => $request->description,
                'selling_price' => $request->selling_price,
                'reorder_point' => $request->reorder_point ?? 0,
                'formula_id' => $request->formula_id,
                'category' => $request->category,
                'source' => $request->source,
            ]);

            $this->logActivity('product_updated', $product);

            if ($request->hasFile('images')) {

                $lastOrder = $product->media()->max('sort_order') ?? 0;

                // Cek apakah produk masih punya gambar
                $hasImage = $product->media()->exists();

                foreach ($request->file('images') as $index => $file) {

                    $path = $file->store('products', 'public');

                    $product->media()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'type' => 'image',

                        // hanya gambar pertama menjadi primary
                        // jika sebelumnya belum ada gambar sama sekali
                        'is_primary' => !$hasImage && $index === 0,

                        'sort_order' => $lastOrder + $index + 1,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroyMedia(Media $media)
    {
        $wasPrimary = $media->is_primary;
        $product = $media->mediable;

        if (
            $media->file_path &&
            Storage::disk('public')->exists($media->file_path)
        ) {
            Storage::disk('public')->delete($media->file_path);
        }

        ActivityLog::create([
            'actor_id' => auth('admin')->id(),
            'actor_type' => \App\Models\Admin::class,
            'type' => 'product_image_deleted',
            'module' => 'product',
            'description' =>
                "Menghapus gambar produk {$product->product_code} - {$product->product_name}",
        ]);

        $media->delete();

        if ($wasPrimary) {

            $newPrimary = $product->media()
                ->orderBy('sort_order')
                ->first();

            if ($newPrimary) {
                $newPrimary->update([
                    'is_primary' => true
                ]);
            }
        }

        return back()->with(
            'success',
            'Gambar berhasil dihapus'
        );
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        if ($product->stock > 0) {
            return redirect()->back()
                ->with(
                    'error',
                    'Produk tidak bisa dihapus karena masih memiliki stok'
                );
        }

        DB::transaction(function () use ($product) {

            foreach ($product->media as $media) {

                if ($media->file_path) {
                    Storage::disk('public')->delete($media->file_path);
                }

                $media->delete();
            }

            $this->logActivity('product_deleted', $product);

            $product->delete();
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    private function logActivity($type, Product $product)
    {
        ActivityLog::create([
            'actor_id' => auth('admin')->id(),
            'actor_type' => \App\Models\Admin::class,
            'type' => $type,
            'module' => 'product',
            'description' => match ($type) {

                'product_created' =>
                "Menambahkan produk {$product->product_code} - {$product->product_name}",

                'product_updated' =>
                "Memperbarui produk {$product->product_code} - {$product->product_name}",

                'product_deleted' =>
                "Menghapus produk {$product->product_code} - {$product->product_name}",
            }
        ]);
    }
}