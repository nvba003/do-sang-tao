<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_tags');
    }
}
