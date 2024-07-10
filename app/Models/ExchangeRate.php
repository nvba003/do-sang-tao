<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function fundSource()
    {
        return $this->belongsTo(FundSource::class);
    }
}
