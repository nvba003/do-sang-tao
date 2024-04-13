<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    // Định nghĩa các cột có thể được gán hàng loạt
    protected $fillable = ['name', 'description'];

    // Quan hệ một danh mục có nhiều sản phẩm
    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
