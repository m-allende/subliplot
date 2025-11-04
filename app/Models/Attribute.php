<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['code','name_es','sort_order','active'];
    protected $casts = ['active'=>'boolean'];

    public function options()
    {
        return $this->hasMany(\App\Models\AttributeOption::class)->orderBy('sort_order');
    }
}
