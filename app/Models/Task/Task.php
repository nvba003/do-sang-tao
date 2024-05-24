<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Customer;

class Task extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $dates = ['due_date'];
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_users')->withPivot('is_primary');
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

    // Thêm mối quan hệ với Customer
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'task_customers', 'task_id', 'customer_id');
    }

}
