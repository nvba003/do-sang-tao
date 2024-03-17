<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Định nghĩa các cột trong bảng mà bạn muốn gán hàng loạt (mass assignable)
    protected $fillable = ['name', 'description', 'due_date', 'status'];

    // Các cột được bảo vệ không cho gán hàng loạt (không bắt buộc)
    protected $guarded = [];

    // Các thuộc tính nên được chuyển đổi sang các kiểu dữ liệu cụ thể (không bắt buộc)
    protected $casts = [
        'due_date' => 'datetime',
    ];
}
