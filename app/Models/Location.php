<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function isParent()
    {
        return is_null($this->parent_id);
    }

    public function isChild()
    {
        return !is_null($this->parent_id);
    }

    public function containers()
    {
        return $this->hasMany(Container::class);
    }
}
