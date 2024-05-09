<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSendo extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function orderDetails()
    {
        return $this->hasMany(OrderSendoDetail::class, 'order_sendo_id');
    }
}
