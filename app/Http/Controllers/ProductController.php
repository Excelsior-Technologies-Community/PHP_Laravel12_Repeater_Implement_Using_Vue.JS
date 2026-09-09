<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFaq;
use App\Models\ProductHighlight;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'images', 'variants', 'specifications', 'faqs', 'highlights'])
            ->withCount(['images', 'variants', 'specifications', 'faqs', 'highlights']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('stock_status') && $request->stock_status !== 'all') {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                      ->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'in_stock') {
                $query->whereColumn('stock_quantity', '>', 'low_stock_threshold');
            }
        }

        $sort = $request->input('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'title_asc') {
            $query->orderBy('title', 'asc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(10)->withQueryString();

        return Inertia::render('Product/Index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands' => Brand::orderBy('name')->get(['id', 'name']),
            'filters' => [
                'search' => $request->search ?? '',
                'category_id' => $request->category_id ?? '',
                'brand_id' => $request->brand_id ?? '',
                'status' => $request->status ?? 'all',
                'stock_status' => $request->stock_status ?? 'all',
                'sort' => $sort,
            ],
            'metrics' => [
                'total' => Product::count(),
                'active' => Product::where('status', 'active')->count(),
                'inactive' => Product::where('status', 'inactive')->count(),
                'low_stock' => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0)->count(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Product/Create', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands' => Brand::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',

            // Images
            'images' => 'nullable|array',
            'images.*' => 'nullable',

            // Variants
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:100',
            'variants.*.color' => 'nullable|string|max:100',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',

            // Specs
            'specifications' => 'nullable|array',
            'specifications.*.spec_key' => 'nullable|string|max:255',
            'specifications.*.spec_value' => 'nullable|string|max:255',
            'specifications.*.sort_order' => 'nullable|integer',

            // FAQs
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:500',
            'faqs.*.answer' => 'nullable|string',
            'faqs.*.sort_order' => 'nullable|integer',

            // Highlights
            'highlights' => 'nullable|array',
            'highlights.*.highlight_text' => 'nullable|string|max:500',
            'highlights.*.sort_order' => 'nullable|integer',
        ]);

        $title = $validated['title'] ?? $validated['name'] ?? 'Untitled Product';
        $description = $validated['description'] ?? $validated['details'] ?? null;
        $status = $validated['status'] ?? 'active';

        DB::transaction(function () use ($validated, $request, $title, $description, $status) {
            $product = Product::create([
                'title' => $title,
                'sku' => $validated['sku'] ?? null,
                'price' => $validated['price'],
                'status' => $status,
                'description' => $description,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
                'category_id' => $validated['category_id'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
            ]);

            // Save Images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $path = $file->store('products', 'public');
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'is_primary' => ($index === 0),
                            'sort_order' => $index + 1,
                        ]);
                    }
                }
            }

            // Save Variants
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $var) {
                    if (!empty($var['size']) || !empty($var['color']) || !empty($var['sku'])) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => $var['size'] ?? null,
                            'color' => $var['color'] ?? null,
                            'sku' => $var['sku'] ?? null,
                            'price' => !empty($var['price']) ? $var['price'] : $product->price,
                            'stock_quantity' => $var['stock_quantity'] ?? 0,
                        ]);
                    }
                }
            }

            // Save Specifications
            if (!empty($validated['specifications'])) {
                foreach ($validated['specifications'] as $idx => $spec) {
                    if (!empty($spec['spec_key']) || !empty($spec['spec_value'])) {
                        ProductSpecification::create([
                            'product_id' => $product->id,
                            'spec_key' => $spec['spec_key'] ?? '',
                            'spec_value' => $spec['spec_value'] ?? '',
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Save FAQs
            if (!empty($validated['faqs'])) {
                foreach ($validated['faqs'] as $idx => $faq) {
                    if (!empty($faq['question']) || !empty($faq['answer'])) {
                        ProductFaq::create([
                            'product_id' => $product->id,
                            'question' => $faq['question'] ?? '',
                            'answer' => $faq['answer'] ?? '',
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Save Highlights
            if (!empty($validated['highlights'])) {
                foreach ($validated['highlights'] as $idx => $hl) {
                    if (!empty($hl['highlight_text'])) {
                        ProductHighlight::create([
                            'product_id' => $product->id,
                            'highlight_text' => $hl['highlight_text'],
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }
        });

        return redirect('/product')->with('success', 'Product created successfully with all repeaters!');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'variants', 'specifications', 'faqs', 'highlights']);

        return Inertia::render('Product/Edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands' => Brand::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',

            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:100',
            'variants.*.color' => 'nullable|string|max:100',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',

            'specifications' => 'nullable|array',
            'specifications.*.spec_key' => 'nullable|string|max:255',
            'specifications.*.spec_value' => 'nullable|string|max:255',
            'specifications.*.sort_order' => 'nullable|integer',

            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:500',
            'faqs.*.answer' => 'nullable|string',
            'faqs.*.sort_order' => 'nullable|integer',

            'highlights' => 'nullable|array',
            'highlights.*.highlight_text' => 'nullable|string|max:500',
            'highlights.*.sort_order' => 'nullable|integer',

            'images' => 'nullable|array',
            'images.*' => 'nullable',
        ]);

        $title = $validated['title'] ?? $validated['name'] ?? $product->title;
        $description = $validated['description'] ?? $validated['details'] ?? $product->description;
        $status = $validated['status'] ?? $product->status ?? 'active';

        DB::transaction(function () use ($validated, $request, $product, $title, $description, $status) {
            $product->update([
                'title' => $title,
                'sku' => $validated['sku'] ?? null,
                'price' => $validated['price'],
                'status' => $status,
                'description' => $description,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
                'category_id' => $validated['category_id'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
            ]);

            // Handle New Uploaded Images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $path = $file->store('products', 'public');
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'is_primary' => false,
                            'sort_order' => 99,
                        ]);
                    }
                }
            }

            // Sync Variants
            $product->variants()->delete();
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $var) {
                    if (!empty($var['size']) || !empty($var['color']) || !empty($var['sku'])) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => $var['size'] ?? null,
                            'color' => $var['color'] ?? null,
                            'sku' => $var['sku'] ?? null,
                            'price' => !empty($var['price']) ? $var['price'] : $product->price,
                            'stock_quantity' => $var['stock_quantity'] ?? 0,
                        ]);
                    }
                }
            }

            // Sync Specifications
            $product->specifications()->delete();
            if (!empty($validated['specifications'])) {
                foreach ($validated['specifications'] as $idx => $spec) {
                    if (!empty($spec['spec_key']) || !empty($spec['spec_value'])) {
                        ProductSpecification::create([
                            'product_id' => $product->id,
                            'spec_key' => $spec['spec_key'] ?? '',
                            'spec_value' => $spec['spec_value'] ?? '',
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Sync FAQs
            $product->faqs()->delete();
            if (!empty($validated['faqs'])) {
                foreach ($validated['faqs'] as $idx => $faq) {
                    if (!empty($faq['question']) || !empty($faq['answer'])) {
                        ProductFaq::create([
                            'product_id' => $product->id,
                            'question' => $faq['question'] ?? '',
                            'answer' => $faq['answer'] ?? '',
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }

            // Sync Highlights
            $product->highlights()->delete();
            if (!empty($validated['highlights'])) {
                foreach ($validated['highlights'] as $idx => $hl) {
                    if (!empty($hl['highlight_text'])) {
                        ProductHighlight::create([
                            'product_id' => $product->id,
                            'highlight_text' => $hl['highlight_text'],
                            'sort_order' => $idx + 1,
                        ]);
                    }
                }
            }
        });

        return redirect('/product')->with('success', 'Product updated successfully with all repeaters!');
    }

    public function setPrimary(Product $product, ProductImage $image)
    {
        ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return redirect()->back()->with('success', 'Primary image updated.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();

        return redirect()->back()->with('success', 'Image deleted.');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            foreach ($product->images as $img) {
                if (Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
                $img->delete();
            }
            $product->variants()->delete();
            $product->specifications()->delete();
            $product->faqs()->delete();
            $product->highlights()->delete();
            $product->delete();
        });

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }

    public function duplicate(Product $product)
    {
        $newProduct = DB::transaction(function () use ($product) {
            $product->load(['variants', 'specifications', 'faqs', 'highlights']);

            $cloned = $product->replicate(['sku']);
            $cloned->title = $product->title . ' (Copy)';
            $cloned->sku = $product->sku ? $product->sku . '-COPY-' . strtoupper(substr(uniqid(), -4)) : null;
            $cloned->save();

            foreach ($product->variants as $var) {
                $clonedVar = $var->replicate();
                $clonedVar->product_id = $cloned->id;
                $clonedVar->sku = $var->sku ? $var->sku . '-COPY' : null;
                $clonedVar->save();
            }

            foreach ($product->specifications as $spec) {
                $clonedSpec = $spec->replicate();
                $clonedSpec->product_id = $cloned->id;
                $clonedSpec->save();
            }

            foreach ($product->faqs as $faq) {
                $clonedFaq = $faq->replicate();
                $clonedFaq->product_id = $cloned->id;
                $clonedFaq->save();
            }

            foreach ($product->highlights as $hl) {
                $clonedHl = $hl->replicate();
                $clonedHl->product_id = $cloned->id;
                $clonedHl->save();
            }

            return $cloned;
        });

        return redirect('/product')->with('success', "Product \"{$product->title}\" duplicated successfully with all repeater data!");
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:products,id',
        ]);

        DB::transaction(function () use ($validated) {
            $products = Product::with('images')->whereIn('id', $validated['ids'])->get();
            foreach ($products as $product) {
                foreach ($product->images as $img) {
                    if (Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                    $img->delete();
                }
                $product->variants()->delete();
                $product->specifications()->delete();
                $product->faqs()->delete();
                $product->highlights()->delete();
                $product->delete();
            }
        });

        return redirect()->back()->with('success', count($validated['ids']) . ' products deleted successfully.');
    }

    public function exportCsv()
    {
        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM
            fputs($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, [
                'ID',
                'Title',
                'SKU',
                'Category',
                'Brand',
                'Price ($)',
                'Status',
                'Stock Quantity',
                'Variants Count',
                'Specs Count',
                'FAQs Count',
                'Highlights Count',
                'Created At',
            ]);

            Product::with(['category', 'brand', 'variants', 'specifications', 'faqs', 'highlights'])
                ->chunk(100, function ($products) use ($handle) {
                    foreach ($products as $p) {
                        fputcsv($handle, [
                            $p->id,
                            $p->title,
                            $p->sku ?? 'N/A',
                            $p->category ? $p->category->name : 'Uncategorized',
                            $p->brand ? $p->brand->name : 'None',
                            $p->price,
                            $p->status,
                            $p->stock_quantity,
                            $p->variants->count(),
                            $p->specifications->count(),
                            $p->faqs->count(),
                            $p->highlights->count(),
                            $p->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="products_export_' . date('Y-m-d_His') . '.csv"');

        return $response;
    }
}
