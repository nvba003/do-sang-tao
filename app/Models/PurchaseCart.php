<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseCart extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function items()
    {
        return $this->hasMany(PurchaseCartItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
