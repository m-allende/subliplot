<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_type_id','name','code',
        'width_mm','height_mm','weight_gsm','color_hex',
        'extra_json','sort_order','active'
    ];

    public function type()
    {
        return $this->belongsTo(AttributeType::class, 'attribute_type_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values')
            ->withPivot(['is_default','sort_order','price_delta_type','price_delta','extra_json'])
            ->withTimestamps();
    }
}
