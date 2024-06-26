<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProductProperty extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    
    public function supplierProduct()
    {
        return $this->belongsTo(SupplierProduct::class);
    }
}
