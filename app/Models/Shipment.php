<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id','carrier','service','tracking_code',
        'shipping_cost_gross','shipping_cost_net','shipping_tax',
        'status','labels_json',
    ];

    protected $casts = [
        'labels_json' => 'array',
    ];

    public function order(){ return $this->belongsTo(Order::class); }
}
