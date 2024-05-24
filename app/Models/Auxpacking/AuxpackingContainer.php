<?php

namespace App\Models\Auxpacking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Container;
use App\Models\Product;
use App\Models\Order;

class AuxpackingContainer extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function product()
    {
        return $this->belongsTo(AuxpackingProduct::class, 'auxpacking_product_id', 'id');
    }
    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id', 'id');
    }
    public function productApi()
    {
        return $this->belongsTo(Product::class, 'product_api_id', 'product_api_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
