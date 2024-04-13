<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;
    //protected $table = 'containers';
    //protected $keyType = 'string'; // Đặt kiểu dữ liệu của khóa chính
    //public $incrementing = false; // Vô hiệu hóa tự tăng vì khóa chính không phải số nguyên
    protected $guarded = ['id'];
    // Mối quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_api_id');//khóa product_id bảng Container liên kết với khóa product_api_id của bảng Product
    }

    // Mối quan hệ với Product
    public function productapi()
    {
        return $this->belongsTo(ProductApi::class, 'product_id', 'id');
    }

    // Mối quan hệ với Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Mối quan hệ với Location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Mối quan hệ sản phẩm với InventoryTransaction
    public function transaction()
    {
        return $this->hasMany(InventoryTransaction::class, 'product_id');
    }

    // Mối quan hệ thùng hàng với InventoryTransaction
    public function inventoryTransaction()
    {
        return $this->hasMany(InventoryTransaction::class, 'container_id');
    }
}
