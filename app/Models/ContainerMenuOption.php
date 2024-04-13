<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerMenuOption extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(ContainerMenuOption::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ContainerMenuOption::class, 'parent_id');
    }

    public function isParent()
    {
        return is_null($this->parent_id);
    }
}
