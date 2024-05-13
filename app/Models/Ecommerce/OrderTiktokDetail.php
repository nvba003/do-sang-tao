<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductApi;//import do khác namespace

class OrderTiktokDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function orderTiktok()
    {
        return $this->belongsTo(OrderTiktok::class);
    }
    public function product()
    {
        return $this->belongsTo(ProductApi::class, 'product_api_id');
    }
}
