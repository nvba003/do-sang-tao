<?php

namespace App\Models\Auxpacking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Platform;
use App\Models\User;

class AuxpackingScan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function auxpackingOrder()
    {
        return $this->belongsTo(AuxpackingOrder::class, 'order_id', 'order_id');
    }
}
