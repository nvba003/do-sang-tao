<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
    public function orderProcess()
    {
        return $this->hasOne(OrderProcess::class);
    }
    public function orderSendo()
    {
        return $this->hasOne(OrderSendo::class, 'order_id');
    }
    
}
