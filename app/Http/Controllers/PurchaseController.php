<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class PurchaseController extends Controller
{
    public function index()
    {
        // Lấy tất cả các sản phẩm với thông tin nhà cung cấp và đánh giá
        $products = Product::with(['suppliers.reviews', 'suppliers' => function ($query) {
            $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
        }])->get();
        
        return view('purchase_summary.index', compact('products'));
    }
}
