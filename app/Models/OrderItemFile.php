<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemFile extends Model
{
    protected $fillable = ['order_item_id','path','original_name','mime','size'];
    public function item(){ return $this->belongsTo(OrderItem::class); }
}
