<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'identification',
        'type',
    ];

    public function addresses()
    {
        return $this->morphMany(Address::class, "parent");
    }

    public function lastAddress()
    {
        return $this->morphOne(Address::class, "parent")->latestOfMany();
    }

    public function emails()
    {
        return $this->morphMany(Email::class, "parent");
    }

    public function lastEmail()
    {
        return $this->morphOne(Email::class, "parent")->latestOfMany();
    }

    public function phones()
    {
        return $this->morphMany(Phone::class, "parent");
    }

    public function lastPhone()
    {
        return $this->morphOne(Phone::class, "parent")->latestOfMany();
    }

    public function photo()
    {
        return $this->morphOne(Photo::class, "parent");
    }
}
