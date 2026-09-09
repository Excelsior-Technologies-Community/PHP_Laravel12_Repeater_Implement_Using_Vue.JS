<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'sku',
        'price',
        'status',
        'description',
        'stock_quantity',
        'low_stock_threshold',
        'category_id',
        'brand_id',
    ];

    protected $appends = ['stock_status'];

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        if ($this->stock_quantity <= ($this->low_stock_threshold ?? 5)) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('sort_order', 'asc');
    }

    public function faqs()
    {
        return $this->hasMany(ProductFaq::class)->orderBy('sort_order', 'asc');
    }

    public function highlights()
    {
        return $this->hasMany(ProductHighlight::class)->orderBy('sort_order', 'asc');
    }
}