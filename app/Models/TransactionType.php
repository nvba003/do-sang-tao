<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'transaction_type_id', 'id');
    }
}
