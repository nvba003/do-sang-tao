<?php

namespace App\Models\Auxpacking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Order;

class AuxpackingProduct extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function summary()
    {
        return $this->belongsTo(AuxpackingProductSummary::class, 'product_api_id', 'product_api_id');
    }
    public function containers()
    {
        return $this->hasMany(AuxpackingContainer::class, 'auxpacking_product_id', 'id');
    }
    public function productApi()
    {
        return $this->belongsTo(Product::class, 'product_api_id', 'product_api_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'auxpacking_order_id');
    }
}
