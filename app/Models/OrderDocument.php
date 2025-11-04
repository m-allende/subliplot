<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDocument extends Model
{
    protected $fillable = [
        'order_id','type','folio','status',
        'receiver_rut','receiver_name','receiver_giro',
        'receiver_address','receiver_country_id','receiver_region_id','receiver_commune_id',
        'subtotal_net','tax_total','grand_total','currency',
        'pdf_path','issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
