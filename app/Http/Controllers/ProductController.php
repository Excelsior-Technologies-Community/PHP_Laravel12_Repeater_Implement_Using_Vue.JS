<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Show product list
     */
    public function index()
    {
        return Inertia::render('Product/Index', [
            'products' => Product::with('images')->latest()->get()
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        return Inertia::render('Product/Create');
    }

    /**
     * Store product with multiple images
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'details'  => 'required',
            'price'    => 'required|numeric',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp'
        ]);

        // Create product
        $product = Product::create(
            $request->only('name', 'details', 'price')
        );

        // Save images in public/products
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time().'_'.Str::random(8).'.'.$image->extension();
                $image->move(public_path('products'), $imageName);

                $product->images()->create([
                    'image' => 'products/'.$imageName
                ]);
            }
        }

        return redirect()->route('product.index');
    }

    /**
     * Show edit form
     */
    public function edit(Product $product)
    {
        return Inertia::render('Product/Edit', [
            'product' => $product->load('images')
        ]);
    }

    /**
     * Update product
     * - Remove old images
     * - Add new images using repeater
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'     => 'required',
            'details'  => 'required',
            'price'    => 'required|numeric',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp'
        ]);

        // Update product fields
        $product->update(
            $request->only('name', 'details', 'price')
        );

        // Delete removed images
        if ($request->filled('remove_images')) {
            foreach ($request->remove_images as $id) {
                $img = ProductImage::find($id);
                if ($img) {
                    $path = public_path($img->image);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $img->delete();
                }
            }
        }

        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time().'_'.Str::random(8).'.'.$image->extension();
                $image->move(public_path('products'), $imageName);

                $product->images()->create([
                    'image' => 'products/'.$imageName
                ]);
            }
        }

        return redirect()->route('product.index');
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return back();
    }
}
