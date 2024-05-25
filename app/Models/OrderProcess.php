<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auxpacking\AuxpackingOrder;

class OrderProcess extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function auxpackingOrder()
    {
        return $this->belongsTo(AuxpackingOrder::class, 'order_id', 'order_id');
    }
    public function platform()
    {
        return $this->hasOne(Platform::class, 'id', 'platform_id');
    }
    public function condition()
    {
        return $this->hasOne(OrderCondition::class, 'id', 'order_condition_id');
    }
    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
    public function cancelAndReturn()
    {
        return $this->hasOne(OrderCancelAndReturn::class, 'order_id', 'order_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
    
}
