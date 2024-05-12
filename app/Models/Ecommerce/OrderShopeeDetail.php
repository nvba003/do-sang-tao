<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShopeeDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function orderShopee()
    {
        return $this->belongsTo(OrderShopee::class);
    }
    public function product()
    {
        return $this->belongsTo(ProductApi::class, 'product_api_id');
    }
}
