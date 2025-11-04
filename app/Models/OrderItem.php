<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','product_id','product_name','product_thumb',
        'uses_quantity','qty_raw','qty_display','qty_real',
        'unit_price_gross','unit_price_net','tax_amount_unit',
        'line_total_gross','line_total_net','line_tax_total',
        'options_json','options_display','options_map',
        'file_disk','file_path','file_name','file_size',
    ];

    protected $casts = [
        'options_json'   => 'array',
        'options_display'=> 'array',
        'options_map'    => 'array',
        'uses_quantity'  => 'boolean',
    ];

    public function order(){ return $this->belongsTo(Order::class); }
    public function product(){ return $this->belongsTo(Product::class); }
    public function files(){ return $this->hasMany(OrderItemFile::class); }

}
