<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSapo extends Model
{
    use HasFactory;
    // Khai báo tên bảng
    protected $table = 'orders_sapo';
    protected $fillable = ['madonhang', 'tenkhachhang', 'sdt', 'diachi', 'chinhanh', 'nguon', 'sanpham', 'tongtien'];
}
