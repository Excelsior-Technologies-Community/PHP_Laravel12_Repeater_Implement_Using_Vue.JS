<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Display product listing.
     *
     * Features:
     * - Search
     * - Sorting
     * - Pagination
     * - Per page
     * - Price filter
     * - Image count filter
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'oldest');
        $perPage = (int) $request->input('per_page', 5);

        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $imageFilter = $request->input('image_filter');

        // Allowed pagination values
        if (!in_array($perPage, [5, 10, 25, 50, 100])) {
            $perPage = 5;
        }

        $query = Product::query()
            ->with(['images', 'primaryImage'])
            ->withCount('images');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== null && trim($search) !== '') {
            $search = trim($search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('details', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Minimum Price
        |--------------------------------------------------------------------------
        */

        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', (float) $minPrice);
        }

        /*
        |--------------------------------------------------------------------------
        | Maximum Price
        |--------------------------------------------------------------------------
        */

        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', (float) $maxPrice);
        }

        /*
        |--------------------------------------------------------------------------
        | Image Count Filter
        |--------------------------------------------------------------------------
        */

        if ($imageFilter === 'no_images') {
            $query->has('images', '=', 0);
        }

        if ($imageFilter === 'with_images') {
            $query->has('images', '>', 0);
        }

        if ($imageFilter === 'multiple_images') {
            $query->has('images', '>', 1);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->paginate($perPage)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::count();

        $totalImages = ProductImage::count();

        $productsWithImages = Product::has('images')->count();

        $productsWithoutImages = Product::doesntHave('images')->count();

        return Inertia::render('Product/Index', [
            'products' => $products,

            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'per_page' => $perPage,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'image_filter' => $imageFilter,
            ],

            'statistics' => [
                'total_products' => $totalProducts,
                'total_images' => $totalImages,
                'products_with_images' => $productsWithImages,
                'products_without_images' => $productsWithoutImages,
            ],
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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::create(
                $request->only('name', 'details', 'price')
            );

            $sortOrder = 0;

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time()
                        . '_' .
                        Str::random(8)
                        . '.'
                        . $image->extension();

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
        });

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
            'product' => $product->load([
                'images',
                'primaryImage',
            ]),
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

            'images' => 'nullable|array',

            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

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
                $this->deleteImageFileAndRecord(
                    $product,
                    $id
                );
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
                $imageName = time()
                    . '_'
                    . Str::random(8)
                    . '.'
                    . $image->extension();

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
        | Make sure one image is primary
        |--------------------------------------------------------------------------
        */

        if (
            $product->images()->exists() &&
            !$product->images()
                ->where('is_primary', true)
                ->exists()
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
     * Set primary image.
     */
    public function setPrimary(
        Product $product,
        ProductImage $image
    ) {
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
    public function reorderImages(
        Request $request,
        Product $product
    ) {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'integer',
        ]);

        DB::transaction(function () use ($request, $product) {
            foreach (
                $request->images as $index => $imageId
            ) {
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
     * FEATURE 7:
     * Delete one repeater image.
     */
    public function destroyImage(
        Product $product,
        ProductImage $image
    ) {
        if ($image->product_id !== $product->id) {
            abort(404);
        }

        $wasPrimary = $image->is_primary;

        $this->deleteImageFileAndRecord(
            $product,
            $image->id
        );

        /*
        |--------------------------------------------------------------------------
        | Select another primary image
        |--------------------------------------------------------------------------
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

        return back()->with(
            'success',
            'Image deleted successfully.'
        );
    }

    /**
     * FEATURE 8:
     * Bulk delete products.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:products,id',
        ]);

        $products = Product::with('images')
            ->whereIn('id', $request->ids)
            ->get();

        foreach ($products as $product) {
            foreach ($product->images as $image) {
                $path = public_path($image->image);

                if (file_exists($path)) {
                    @unlink($path);
                }
            }

            $product->delete();
        }

        return back()->with(
            'success',
            count($request->ids) . ' product(s) deleted successfully.'
        );
    }

    /**
     * FEATURE 9:
     * Export products to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $search = $request->input('search');

        $query = Product::withCount('images')
            ->oldest();

        if ($search !== null && trim($search) !== '') {
            $search = trim($search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('details', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);
            });
        }

        $products = $query->get();

        $fileName = 'products-' . now()->format('Y-m-d-H-i-s') . '.csv';

        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Name',
                'Details',
                'Price',
                'Images',
                'Created At',
            ]);

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->id,
                    $product->name,
                    $product->details,
                    $product->price,
                    $product->images_count,
                    $product->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName);
    }

    /**
     * FEATURE 10:
     * Duplicate product with all images.
     */
    public function duplicate(Product $product)
    {
        $product->load('images');

        DB::transaction(function () use ($product) {
            $newProduct = Product::create([
                'name' => $product->name . ' - Copy',
                'details' => $product->details,
                'price' => $product->price,
            ]);

            foreach ($product->images as $image) {
                $newImagePath = null;

                $oldPath = public_path($image->image);

                if (file_exists($oldPath)) {
                    $extension = pathinfo(
                        $oldPath,
                        PATHINFO_EXTENSION
                    );

                    $newFileName = time()
                        . '_'
                        . Str::random(8)
                        . '.'
                        . $extension;

                    $newPath = public_path(
                        'products/' . $newFileName
                    );

                    copy($oldPath, $newPath);

                    $newImagePath =
                        'products/' . $newFileName;
                }

                if ($newImagePath) {
                    $newProduct->images()->create([
                        'image' => $newImagePath,
                        'sort_order' => $image->sort_order,
                        'is_primary' => $image->is_primary,
                    ]);
                }
            }
        });

        return back()->with(
            'success',
            'Product duplicated successfully.'
        );
    }

    /**
     * Delete product and physical image files.
     */
    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            $path = public_path($image->image);

            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $product->delete();

        return back()->with(
            'success',
            'Product deleted successfully.'
        );
    }

    /**
     * Helper for deleting a single image.
     */
    private function deleteImageFileAndRecord(
        Product $product,
        int $imageId
    ): void {
        $image = ProductImage::where('product_id', $product->id)
            ->where('id', $imageId)
            ->first();

        if (!$image) {
            return;
        }

        $path = public_path($image->image);

        if (file_exists($path)) {
            @unlink($path);
        }

        $image->delete();
    }
}
