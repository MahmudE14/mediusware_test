<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Variant;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $fillable = [
        'variant',
        'variant_id',
        'product_id',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}
