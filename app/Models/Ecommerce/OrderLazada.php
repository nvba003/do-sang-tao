<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;//import do khác namespace

class OrderLazada extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function details()
    {
        return $this->hasMany(OrderLazadaDetail::class, 'order_lazada_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
