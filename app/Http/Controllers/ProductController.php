<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Show product list.
     */
    public function index()
    {
        $products = Product::with(['images', 'primaryImage'])
            ->latest()
            ->get();

        return Inertia::render('Product/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return Inertia::render('Product/Create');
    }

    /**
     * Store product with multiple images.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'required|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product = Product::create(
            $request->only('name', 'details', 'price')
        );

        $sortOrder = 0;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $imageName = time() . '_' . Str::random(8) . '.' . $image->extension();

                $image->move(
                    public_path('products'),
                    $imageName
                );

                $product->images()->create([
                    'image' => 'products/' . $imageName,
                    'sort_order' => $sortOrder,
                    'is_primary' => $sortOrder === 0,
                ]);

                $sortOrder++;
            }
        }

        return redirect()
            ->route('product.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Product/Edit', [
            'product' => $product->load(['images', 'primaryImage']),
        ]);
    }

    /**
     * Update product.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'required|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
        ]);

        $product->update(
            $request->only('name', 'details', 'price')
        );

        /*
        |--------------------------------------------------------------------------
        | Remove existing images
        |--------------------------------------------------------------------------
        */

        if ($request->filled('remove_images')) {

            foreach ($request->remove_images as $id) {

                $img = ProductImage::where('product_id', $product->id)
                    ->where('id', $id)
                    ->first();

                if ($img) {

                    $path = public_path($img->image);

                    if (file_exists($path)) {
                        unlink($path);
                    }

                    $wasPrimary = $img->is_primary;

                    $img->delete();

                    /*
                    | If primary image was deleted,
                    | automatically select the first remaining image.
                    */
                    if ($wasPrimary) {

                        $newPrimary = $product->images()
                            ->orderBy('sort_order')
                            ->first();

                        if ($newPrimary) {
                            $newPrimary->update([
                                'is_primary' => true,
                            ]);
                        }
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Add new images
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('images')) {

            $lastOrder = $product->images()->max('sort_order');

            $sortOrder = is_null($lastOrder)
                ? 0
                : $lastOrder + 1;

            foreach ($request->file('images') as $image) {

                $imageName = time() . '_' . Str::random(8) . '.' . $image->extension();

                $image->move(
                    public_path('products'),
                    $imageName
                );

                $product->images()->create([
                    'image' => 'products/' . $imageName,
                    'sort_order' => $sortOrder,
                    'is_primary' => false,
                ]);

                $sortOrder++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Make sure one image is primary when images exist
        |--------------------------------------------------------------------------
        */

        if (
            $product->images()->exists() &&
            !$product->images()->where('is_primary', true)->exists()
        ) {
            $firstImage = $product->images()
                ->orderBy('sort_order')
                ->first();

            $firstImage->update([
                'is_primary' => true,
            ]);
        }

        return redirect()
            ->route('product.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Set an image as the primary image.
     */
    public function setPrimary(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(404);
        }

        DB::transaction(function () use ($product, $image) {

            $product->images()->update([
                'is_primary' => false,
            ]);

            $image->update([
                'is_primary' => true,
            ]);
        });

        return back()->with(
            'success',
            'Primary image updated successfully.'
        );
    }

    /**
     * Reorder product images.
     */
    public function reorderImages(Request $request, Product $product)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'integer',
        ]);

        DB::transaction(function () use ($request, $product) {

            foreach ($request->images as $index => $imageId) {

                ProductImage::where('id', $imageId)
                    ->where('product_id', $product->id)
                    ->update([
                        'sort_order' => $index,
                    ]);
            }
        });

        return back()->with(
            'success',
            'Image order updated successfully.'
        );
    }

    /**
     * Delete product and all physical image files.
     */
    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {

            $path = public_path($image->image);

            if (file_exists($path)) {
                unlink($path);
            }
        }

        $product->delete();

        return back()->with(
            'success',
            'Product deleted successfully.'
        );
    }
}