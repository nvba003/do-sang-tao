<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSupplierLink extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplierProduct()
    {
        return $this->belongsTo(SupplierProduct::class);
    }

    public function supplierProductSku()
    {
        return $this->belongsTo(SupplierProductSku::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
