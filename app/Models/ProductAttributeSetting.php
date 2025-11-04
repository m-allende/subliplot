<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeSetting extends Model
{
    protected $fillable = [
        'product_id','attribute_type_id',
        'enabled','required','multi_select','show_as','sort_order'
    ];

    public function product(){ return $this->belongsTo(Product::class); }
    public function type(){ return $this->belongsTo(AttributeType::class,'attribute_type_id'); }
}
