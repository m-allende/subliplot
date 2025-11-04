<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id','name','subtitle','description',
        'uses_size','uses_paper','uses_bleed','uses_finish','uses_material',
        'uses_shape','uses_print_side','uses_mounting','uses_rolling','uses_holes', 'uses_quantity',
        'active','sort_order',
    ];

    protected $casts = [
        'uses_size'        => 'bool',
        'uses_paper'       => 'bool',
        'uses_bleed'       => 'bool',
        'uses_finish'      => 'bool',
        'uses_material'    => 'bool',
        'uses_shape'       => 'bool',
        'uses_print_side'  => 'bool',
        'uses_mounting'    => 'bool',
        'uses_rolling'     => 'bool',
        'uses_holes'       => 'bool',
        'uses_quantity'    => 'bool',
        'active'           => 'bool',
    ];

    // Para DataTables: foto principal inline
    protected $appends = ['primary_photo'];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function photos()
    {
        return $this->morphMany(\App\Models\Photo::class, 'imageable');
    }

    public function primaryPhoto()
    {
        return $this->photos()->where('is_primary', true)->latest('id')->first();
    }

    public function getPrimaryPhotoAttribute(): ?array
    {
        $p = $this->primaryPhoto();
        return $p ? [
            'id'  => $p->id,
            'url' => $p->url,     // accessor de Photo: asset('uploads/...') => respeta :8112
            'path'=> $p->path,
        ] : null;
    }

    public function attributeSettings()
    {
        return $this->hasMany(ProductAttributeSetting::class);
    }

    public function attributeValues()
    {
        // Pivot con metacampos (is_default, sort_order, price_delta, etc.)
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values')
            ->withPivot(['is_default','sort_order','price_delta_type','price_delta','extra_json'])
            ->withTimestamps();
    }

    public function attributesByType(string $code)
    {
        return $this->attributeValues()
            ->whereHas('type', function($q) use ($code) {
                $q->where('attribute_types.code', $code);
            })
            ->select('attribute_values.id', 'attribute_values.name')
            ->orderBy('attribute_values.sort_order', 'asc')
            ->get()
            ->map(fn($a) => ['id' => $a->id, 'name' => $a->name])
            ->values()
            ->toArray();
    }



}
