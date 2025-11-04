<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'public_uid','user_id','cookie_id',
        'buyer_name','buyer_email','buyer_phone','notes',
        'currency','tax_rate','items_count','qty_total',
        'subtotal_net','tax_total','grand_total',
        'status','payment_status','meta_json'
    ];

    protected $casts = [
        'meta_json' => 'array',
        'tax_rate'  => 'decimal:2',
    ];

    protected static function booted() {
        static::creating(function($m){
            if (empty($m->public_uid)) $m->public_uid = (string) Str::uuid();
        });
    }

    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function addresses(): HasMany { return $this->hasMany(OrderAddress::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function shipments(): HasMany { return $this->hasMany(Shipment::class); }
    public function logs(): HasMany { return $this->hasMany(OrderStatusLog::class); }

    public function documents() {
        return $this->hasMany(OrderDocument::class);
    }
    public function latestDocument() {
        return $this->hasOne(OrderDocument::class)->latestOfMany();
    }

}
