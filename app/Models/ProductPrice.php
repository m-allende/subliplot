<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id',
        'size_id',
        'paper_id',
        'bleed_id',
        'finish_id',
        'material_id',
        'shape_id',
        'print_side_id',
        'mounting_id',
        'rolling_id',
        'hole_id',
        'quantity_id',
        'price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
