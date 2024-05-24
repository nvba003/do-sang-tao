<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
    public function orderProcess()
    {
        return $this->hasOne(OrderProcess::class);
    }
    public function orderSendo()
    {
        return $this->hasOne(OrderSendo::class, 'order_id');
    }
    public function platform()
    {
        return $this->hasOne(Platform::class, 'id', 'platform_id');
    }
    public function customerAccount()
    {
        return $this->hasOne(CustomerAccount::class, 'id', 'customer_account_id');
    }
    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
    public function finances()
    {
        return $this->hasMany(OrderFinance::class, 'order_id');
    }
    
}
