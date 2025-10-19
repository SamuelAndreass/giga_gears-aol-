<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\CustomerProfile;
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public function sellerProfile(){
        return $this->hasOne(SellerStore::class);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_seller',
    ];

    protected $casts = [
        'is_seller' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected static function booted()
    {
        static::created(function ($user) {
            if ($user->role === 'customer') {
                CustomerProfile::create([
                    'user_id' => $user->id,
                    'customer_name' => $user->name,
                ]);
            }
        });
    }
    public function customerProfile(){
        return $this->hasOne(CustomerProfile::class);
    }

    public function sellerStore(){ return $this->hasOne(Sellerstore::class);}
    
    public function cart(){ return $this->hasOne(Cart::class);}

    public function storeReview(){ return $this->hasMany(SellerStore::class);}

    public function productReview() {return $this->hasMany(ProductReview::class);}
}
