<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierLevel extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(SupplierLevelDetail::class, 'supplier_level_id');
    }
}
