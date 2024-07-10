<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            $transaction->update(['remaining_amount' => $transaction->amount]);
        });
    }

    public function usageLogs()
    {
        return $this->hasMany(FundUsageLog::class);
    }

    public static function calculateVND($amountInCNY)
    {
        $transactions = self::where('remaining_amount', '>', 0)->orderBy('transaction_date')->get();

        $totalAmountInVND = 0;
        $remainingAmount = $amountInCNY;

        foreach ($transactions as $transaction) {
            if ($transaction->remaining_amount >= $remainingAmount) {
                $totalAmountInVND += $remainingAmount * $transaction->exchange_rate;

                // Log the usage
                FundUsageLog::create([
                    'fund_transaction_id' => $transaction->id,
                    'used_amount' => $remainingAmount,
                    'exchange_rate' => $transaction->exchange_rate,
                ]);

                $transaction->remaining_amount -= $remainingAmount;
                $transaction->save();

                break;
            } else {
                $totalAmountInVND += $transaction->remaining_amount * $transaction->exchange_rate;

                // Log the usage
                FundUsageLog::create([
                    'fund_transaction_id' => $transaction->id,
                    'used_amount' => $transaction->remaining_amount,
                    'exchange_rate' => $transaction->exchange_rate,
                ]);

                $remainingAmount -= $transaction->remaining_amount;
                $transaction->remaining_amount = 0;
                $transaction->save();
            }
        }

        return $totalAmountInVND;
    }
}
