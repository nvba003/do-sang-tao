<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FundTransaction;

class FundController extends Controller
{
    public function calculateVND(Request $request)
    {
        $amountInCNY = $request->input('amount_in_cny');
        $amountInVND = FundTransaction::calculateVND($amountInCNY);

        return response()->json(['amount_in_vnd' => $amountInVND]);
    }
}
