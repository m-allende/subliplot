<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id','type',
        'line1','line2','reference',
        'country_id','region_id','commune_id',
        'country_name','region_name','commune_name',
        'postal_code','latitude','longitude',
    ];

    public function order(){ return $this->belongsTo(Order::class); }
    public function country(){ return $this->belongsTo(Country::class); }
    public function region(){ return $this->belongsTo(Region::class); }
    public function commune(){ return $this->belongsTo(Commune::class); }
}
