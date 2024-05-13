<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductApi;//import do khác namespace

class OrderTikiDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function orderTiki()
    {
        return $this->belongsTo(OrderTiki::class);
    }
    public function product()
    {
        return $this->belongsTo(ProductApi::class, 'product_api_id');
    }
}
