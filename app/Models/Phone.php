<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $fillable = [
        'kind','country_code','number','is_default',
    ];

    public function phoneable()
    {
        return $this->morphTo();
    }

    // Accessor útil para mostrar
    public function getFullAttribute(): string
    {
        return trim(($this->country_code ?: '') . ' ' . ($this->number ?: ''));
    }
}
