<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeType extends Model
{
    protected $fillable = ['code','name','description','sort_order','active'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_type_id');
    }

    // (opcional) configuración por producto
    public function productSettings()
    {
        return $this->hasMany(ProductAttributeSetting::class);
    }
}
