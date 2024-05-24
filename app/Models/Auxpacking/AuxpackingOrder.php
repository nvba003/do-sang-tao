<?php

namespace App\Models\Auxpacking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProcess;

class AuxpackingOrder extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function products()
    {
        return $this->hasMany(AuxpackingProduct::class, 'auxpacking_order_id', 'id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function orderProcess()
    {
        return $this->hasOne(OrderProcess::class, 'order_id', 'order_id');
    }
}
