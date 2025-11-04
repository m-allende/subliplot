<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model {
  protected $fillable = ['cart_id','product_id','qty','config_json','unit_price','line_total'];
  protected $casts = ['config_json'=>'array'];
  public function cart(){ return $this->belongsTo(Cart::class); }
  public function product(){ return $this->belongsTo(Product::class); }
  public function quantity(){ return $this->belongsTo(AttributeValue::class, 'qty'); }
}
