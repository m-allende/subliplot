<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'line1','line2','reference',
        'country_id','region_id','commune_id',
        'postal_code','latitude','longitude',
        'is_primary',
    ];

    // Polymorphic inverse
    public function addressable()
    {
        return $this->morphTo();
    }

    // Geo relations
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}
