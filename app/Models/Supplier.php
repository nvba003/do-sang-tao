<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function deliveries()
    {
        return $this->hasMany(SupplierDelivery::class);
    }

    public function products()
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function notes()
    {
        return $this->hasMany(SupplierNote::class);
    }

    public function reviews()
    {
        return $this->hasMany(SupplierReview::class);
    }

    public function issues()
    {
        return $this->hasMany(SupplierIssue::class);
    }
    public function group()
    {
        return $this->belongsTo(SupplierGroup::class, 'supplier_group_id');
    }
    public function levelDetail()
    {
        return $this->belongsTo(SupplierLevelDetail::class, 'supplier_level_detail_id');
    }
}
