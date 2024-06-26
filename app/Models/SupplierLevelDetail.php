<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierLevelDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function level()
    {
        return $this->belongsTo(SupplierLevel::class, 'supplier_level_id');
    }
}
