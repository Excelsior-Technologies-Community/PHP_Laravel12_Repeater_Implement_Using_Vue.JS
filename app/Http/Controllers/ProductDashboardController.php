<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductDashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $inactiveProducts = Product::where('status', 'inactive')->count();
        $totalVariants = ProductVariant::count();
        $totalSpecs = ProductSpecification::count();
        $totalImages = ProductImage::count();
        $totalCategories = Category::count();
        $totalBrands = Brand::count();

        $totalInventoryQty = (int) Product::sum('stock_quantity');
        $totalInventoryValue = (float) Product::selectRaw('SUM(price * stock_quantity) as total_val')->value('total_val') ?? 0;

        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->count();

        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();

        $recentProducts = Product::with(['category', 'brand', 'images', 'variants'])
            ->latest()
            ->take(5)
            ->get();

        $categoryDistribution = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(6)
            ->get(['id', 'name', 'products_count']);

        $brandDistribution = Brand::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(6)
            ->get(['id', 'name', 'products_count']);

        return Inertia::render('Product/Dashboard', [
            'metrics' => [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'inactive_products' => $inactiveProducts,
                'total_variants' => $totalVariants,
                'total_specs' => $totalSpecs,
                'total_images' => $totalImages,
                'total_categories' => $totalCategories,
                'total_brands' => $totalBrands,
                'inventory_qty' => $totalInventoryQty,
                'inventory_val' => $totalInventoryValue,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount,
            ],
            'recent_products' => $recentProducts,
            'category_distribution' => $categoryDistribution,
            'brand_distribution' => $brandDistribution,
        ]);
    }
}