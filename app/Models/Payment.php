<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id','method','amount','currency','status',
        'processor_id','payload_json','paid_at',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'paid_at'      => 'datetime',
    ];

    public function order(){ return $this->belongsTo(Order::class); }
}
