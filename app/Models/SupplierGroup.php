<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierGroup extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'supplier_group_id');
    }
}
