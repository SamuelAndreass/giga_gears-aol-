<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SellerStore extends Model
{
    //
    protected $fillable = ['user_id', 'store_name', 'store_logo', 'store_phone', 'store_address', 'status'];

    public function user(){
        return $this->belongsTo(User::class);
    }   

    public function products(){ return $this->hasMany(Product::class);}

    public function reviews()  { return $this->hasMany(StoreReview::class);}

    protected static function boot() {
        parent::boot();
        static::creating(function ($order) {
            $last = self::latest('id')->first();
            $next = $last ? $last->id + 1 : 1;
            $order->store_code = 'STR-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        });
    }

    public function approve($adminId){
        $this->update([
            'status' => 'approved',
            'verified_at' => now(),
            'verified_by' => $adminId,
        ]);
    }

    public function suspend($adminId){
        $this->update([
            'status' => 'suspend',
            'verified_at' => now(),
            'verified_by' => $adminId,
        ]);
    }

}
