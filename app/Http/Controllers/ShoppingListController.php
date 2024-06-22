<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\Supplier;

class ShoppingListController extends Controller
{
    public function index()
    {
        $shoppingLists = ShoppingList::with(['products.suppliers.reviews', 'products.suppliers' => function ($query) {
            $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
        }])->get();
        
        return view('shopping_lists.index', compact('shoppingLists'), ['header' => 'Giỏ hàng']);
    }
}
