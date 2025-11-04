<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'disk','path','original_name','mime','size','width','height',
        'is_primary','sort_order','title','alt','caption',
    ];

    protected $appends = ['url'];

    public function imageable()
    {
        return $this->morphTo();
    }

    // URL pública a la imagen
    public function getUrlAttribute(): string
    {
        $disk = $this->disk ?: 'public_uploads';
        return Storage::disk($disk)->url($this->path);
    }
}
