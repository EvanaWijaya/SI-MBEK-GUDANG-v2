<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\Product;
use App\Models\Media;
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

        $productCode = Product::generateProductCode('pakan');

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
        $request->validate([
            'product_name' => 'required|string|max:255',
            'selling_price' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'formula_id' => 'nullable|exists:formulas,id',
            'category' => 'required|in:pakan,obat',
            'source' => 'required|in:production,purchase',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

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
        $request->validate([
            'product_name' => 'required|string|max:255',
            'selling_price' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'formula_id' => 'nullable|exists:formulas,id',
            'category' => 'required|in:pakan,obat',
            'source' => 'required|in:production,purchase',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->source === 'production' && !$request->formula_id) {
            return redirect()->back()
                ->withErrors([
                    'formula_id' => 'Produk produksi wajib memiliki formula'
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

            if ($request->hasFile('images')) {

                $lastOrder = $product->media()
                    ->max('sort_order') ?? 0;

                foreach ($request->file('images') as $index => $file) {

                    $path = $file->store('products', 'public');
                    $isFirstImage = $product->media()->count() === 0;

                    $product->media()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'type' => 'image',
                        'is_primary' => $isFirstImage && $index === 0,
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
        if ($media->file_path) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

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

            $product->delete();
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}