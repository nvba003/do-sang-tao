<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_api_id', 'product_api_id');
    }
}
