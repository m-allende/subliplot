<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $fillable = ['region_id','name','code'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
