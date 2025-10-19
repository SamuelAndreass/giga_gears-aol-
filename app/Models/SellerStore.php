<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SellerStore extends Model
{
    //
    protected $fillable = ['user_id', 'store_name', 'store_logo', 'store_phone', 'store_address'];

    public function user(){
        return $this->belongsTo(User::class);
    }   

    public function products(){ return $this->hasMany(Product::class);}

public function reviews()  { return $this->hasMany(StoreReview::class);}

}
