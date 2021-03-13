<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariantPrice;


class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    public function variantPrices()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }
}
