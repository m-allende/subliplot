<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rut',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function addresses() { return $this->morphMany(Address::class, 'addressable'); }
    public function phones()    { return $this->morphMany(Phone::class,   'phoneable'); }
    public function photos()    { return $this->morphMany(Photo::class,   'imageable'); }

     // Helpers
    public function primaryAddress()
    {
        return $this->addresses()->where('is_primary', true)->first();
    }

    public function primaryPhone()
    {
        return $this->phones()->where('is_default', true)->first();
    }
    
    public function avatarUrl(): string {
        $p = $this->photos()->where('is_primary',true)->first();
        return $p
            ? \Storage::disk($p->disk)->url($p->path)
            : 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($this->email))).'?s=160&d=identicon';
    }

}
