<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'details', 'price'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
