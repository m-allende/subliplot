<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    protected $fillable = ['attribute_id','value','name_es','sort_order','active'];
    protected $casts = ['active'=>'boolean'];

    public function attribute()
    {
        return $this->belongsTo(\App\Models\Attribute::class);
    }
}
