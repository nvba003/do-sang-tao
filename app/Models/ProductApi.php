<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductApi extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function containers()
    {
        return $this->hasMany(Container::class, 'product_id');
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'product_id', 'id');
    }
}
