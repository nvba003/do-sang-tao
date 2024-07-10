<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\Supplier;
use App\Models\PurchaseCart;
use App\Models\PurchaseCartItem;
use App\Models\LogisticsDelivery;

class PurchaseController extends Controller
{
    public function index()
    {
        $productGroups = ProductGroup::all();
        $suppliers = Supplier::all();
        // Lấy 20 sản phẩm đầu tiên với thông tin nhà cung cấp và đánh giá
        $products = Product::with(['supplierLinks.supplier.reviews', 'supplierLinks.supplierProduct.supplierSkus', 'purchaseProducts', 'orderDetails'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('purchases.index', compact('products', 'productGroups', 'suppliers'), ['header' => 'Đặt hàng']);
    }

    public function loadMore(Request $request)
    {
        if ($request->ajax()) {
            $skip = $request->input('skip', 0);
            $products = Product::with(['supplierLinks.supplier.reviews', 'supplierLinks.supplierProduct.supplierSkus'])
                ->orderBy('created_at', 'desc')
                ->skip($skip)
                ->take(20)
                ->get();

            return response()->json($products);
        }
        return abort(404);
    }

    public function showCart(Request $request)
    {
        $perPage = $request->get('perPage', 15);
        $suppliers = Supplier::all();
        $logisticsDeliveries = LogisticsDelivery::all();
        $query = PurchaseCart::with(['items.product', 'supplier'])->orderBy('updated_at', 'desc');
        $purchaseCarts = $query->paginate($perPage);
        if ($request->ajax()) {
            return response()->json([
                'purchaseCarts' => $purchaseCarts->items(),
                'links' => $purchaseCarts->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
        } else {
            $initialData = json_encode([
                'purchaseCarts' => $purchaseCarts->items(),
                'links' => $purchaseCarts->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
            return view('purchases.cart', compact(
                'purchaseCarts',
                'logisticsDeliveries',
                'suppliers',
                'initialData'),
                ['header' => 'Giỏ hàng']
            );         
        }
    }

    public function create()//cart
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)//cart
    {
        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_name' => 'required|string',
            'notes' => 'nullable|string',
            'items.*.product_api_id' => 'required|exists:products,product_api_id',
            'items.*.supplier_product_sku_id' => 'nullable|exists:supplier_product_skus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $purchaseCart = PurchaseCart::create([
            'supplier_id' => $validatedData['supplier_id'],
            'supplier_name' => $validatedData['supplier_name'],
            'notes' => $validatedData['notes'] ?? null,
        ]);

        foreach ($validatedData['items'] as $item) {
            PurchaseCartItem::create([
                'purchase_cart_id' => $purchaseCart->id,
                'product_api_id' => $item['product_api_id'],
                'supplier_product_sku_id' => $item['supplier_product_sku_id'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Đơn hàng đã được tạo thành công.');
    }
}
