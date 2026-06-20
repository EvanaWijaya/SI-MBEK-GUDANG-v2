<?php

namespace App\Http\Controllers\Owner;

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

        return view('owner.product.index', compact('products'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $formulas = Formula::where('is_active', true)
            ->orderBy('formula_name')
            ->get();

        return view('owner.product.create', compact('formulas'));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $request->merge([
            'selling_price' => preg_replace('/[^0-9]/', '', $request->selling_price)
        ]);

        $request->validate([
            'product_name' => 'required|string|max:255',
            'selling_price' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'formula_id' => 'nullable|exists:formulas,id',
            'category' => 'required|in:pakan,obat',
            'source' => 'required|in:production,purchase',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
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

            $product = Product::create([
                'product_name' => $request->product_name,
                'description' => $request->description,
                'selling_price' => $request->selling_price,
                'stock' => 0,
                'reorder_point' => $request->reorder_point ?? 0,
                'formula_id' => $request->formula_id,
                'category' => $request->category,
                'source' => $request->source,
                'created_by' => auth('owner')->id(),
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
            ->route('owner.products.index')
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

        return view('owner.product.show', compact(
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
            'selling_price' => preg_replace('/[^0-9]/', '', $request->selling_price)
        ]);

        $request->validate([
            'product_code' => 'required|string|max:50|unique:products,product_code,' . $product->id,
            'product_name' => 'required|string|max:255',
            'selling_price' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'formula_id' => 'nullable|exists:formulas,id',
            'category' => 'required|string|max:100',
            'source' => 'required|in:production,purchase',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
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
                'product_code' => $request->product_code,
                'product_name' => $request->product_name,
                'description' => $request->description,
                'selling_price' => $request->selling_price,
                'reorder_point' => $request->reorder_point ?? 0,
                'formula_id' => $request->formula_id,
                'category' => $request->category,
                'source' => $request->source,
            ]);

            if ($request->hasFile('images')) {

                $lastSortOrder = $product->media()->max('sort_order') ?? 0;

                foreach ($request->file('images') as $index => $file) {

                    $path = $file->store('products', 'public');

                    $product->media()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'type' => 'image',
                        'is_primary' => $product->media()->count() === 0 && $index === 0,
                        'sort_order' => $lastSortOrder + $index + 1,
                    ]);
                }
            }
        });

        return redirect()
            ->route('owner.products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroyMedia(Media $media)
    {

        if ($media->is_primary) {

            $nextImage = $media->product
                ->media()
                ->where('id', '!=', $media->id)
                ->first();

            if ($nextImage) {
                $nextImage->update([
                    'is_primary' => true
                ]);
            }
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
            ->route('owner.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}