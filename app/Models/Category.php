<?php

// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','description','sort_order'];

    // Para que venga en el JSON de DataTables
    protected $appends = ['last_photo'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function photos()
    {
        return $this->morphMany(\App\Models\Photo::class, 'imageable');
    }

    public function getLastPhotoAttribute(): ?array
    {
        $p = $this->photos()
            ->where('is_primary', true)
            ->latest('id')
            ->first();

        if (!$p) return null;

        // Photo ya expone ->url (según definimos antes)
        return [
            'id'   => $p->id,
            'url'  => $p->url,           // ej: http://localhost:8112/uploads/...
            'path' => $p->path,          // ej: users/abc.webp
        ];
    }
}
