<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['country_id','code','name','ordinal'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function communes()
    {
        return $this->hasMany(Commune::class);
    }
}
