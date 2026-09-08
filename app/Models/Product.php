<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'details',
        'price',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true);
    }
}