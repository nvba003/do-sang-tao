<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    //protected $primaryKey = 'product_api_id';
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function containers()
    {
        return $this->hasMany(Container::class, 'product_id');
    }
}
