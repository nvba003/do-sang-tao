<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductApi;//import do khác namespace

class OrderLazadaDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function orderLazada()
    {
        return $this->belongsTo(OrderLazada::class);
    }
    public function product()
    {
        return $this->belongsTo(ProductApi::class, 'product_api_id');
    }
}
