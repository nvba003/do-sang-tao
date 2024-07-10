<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseCartItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function purchaseCart()
    {
        return $this->belongsTo(PurchaseCart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_api_id', 'product_api_id');
    }

    public function supplierProductSku()
    {
        return $this->belongsTo(SupplierProductSku::class);
    }
}
