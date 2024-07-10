<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products()
    {
        return $this->hasMany(PurchaseProduct::class);
    }

    public function fund()
    {
        return $this->belongsTo(FundUsageLog::class);
    }

    public function logisticsDelivery()
    {
        return $this->belongsTo(LogisticsDelivery::class);
    }
}
