<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class PurchaseController extends Controller
{
    public function index()
    {
        // Lấy tất cả các sản phẩm với thông tin nhà cung cấp và đánh giá
        $products = Product::with(['supplierLinks.supplier.reviews', 'supplierLinks.supplierProduct.supplierSkus'])->get();
        
        return view('purchases.index', compact('products'), ['header' => 'Đặt hàng']);
    }
}
