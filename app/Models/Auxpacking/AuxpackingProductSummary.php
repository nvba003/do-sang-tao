<?php

namespace App\Models\Auxpacking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuxpackingProductSummary extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function products()
    {
        return $this->hasMany(AuxpackingProduct::class, 'product_api_id', 'product_api_id');
    }
}
