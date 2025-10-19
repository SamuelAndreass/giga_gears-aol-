<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['user_id','order_date', 'total_amount', 'status'];

    public function item(){
        return $this->hasmany(OrderItem::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
