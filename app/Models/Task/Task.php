<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class Task extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $dates = ['due_date'];
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'task_users')->withPivot('is_creator', 'is_primary', 'is_secondary');
    // }
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_users')->withPivot('role');
    }
    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tags', 'task_id', 'tag_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'task_orders', 'task_id', 'order_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'task_products', 'task_id', 'product_id');
    }
    // Thêm mối quan hệ với Customer
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'task_customers', 'task_id', 'customer_id');
    }
     
    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    // Các task phụ thuộc vào task này
    public function dependents()
    {
        return $this->belongsToMany(Task::class, 'task_relations', 'task_id', 'related_task_id')
                    ->withPivot('relation_type')
                    ->wherePivot('relation_type', 1); // 1 là loại quan hệ phụ thuộc
    }

    // Các task mà task này phụ thuộc
    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_relations', 'related_task_id', 'task_id')
                    ->withPivot('relation_type')
                    ->wherePivot('relation_type', 1); // 1 là loại quan hệ phụ thuộc
    }

    // Các task liên quan (không phải phụ thuộc)
    public function relatedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_relations', 'task_id', 'related_task_id')
                    ->withPivot('relation_type')
                    ->wherePivot('relation_type', 2); // 2 là loại quan hệ liên quan
    }

}
