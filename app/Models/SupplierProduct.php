<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProduct extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_api_id', 'product_api_id');
    }

    public function reviews()
    {
        return $this->belongsTo(SupplierReview::class, 'supplier_id');
    }
}
