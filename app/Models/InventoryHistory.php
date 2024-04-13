<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function inventoryTransaction()
    {
        return $this->belongsTo(InventoryTransaction::class, 'transaction_id');
    }

}
