<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProductSku extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function supplierProduct()
    {
        return $this->belongsTo(SupplierProduct::class);
    }

    public function productLinks()
    {
        return $this->hasMany(ProductSupplierLink::class);
    }
}
