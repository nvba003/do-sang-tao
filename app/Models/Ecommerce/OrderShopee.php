<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShopee extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function details()
    {
        return $this->hasMany(OrderShopeeDetail::class, 'order_shopee_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
