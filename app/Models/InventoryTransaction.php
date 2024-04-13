<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    // Mối quan hệ với Container
    public function containers()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }

    // Mối quan hệ với TransactionType
    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id', 'id');
    }

    // Mối quan hệ với ProductApi
    public function productApi()
    {
        return $this->belongsTo(ProductApi::class, 'product_id', 'id');
    }

    // Mối quan hệ với InventoryHistory
    public function inventoryHistory()
    {
        return $this->hasOne(InventoryHistory::class, 'transaction_id');
    }

    // Mối quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
