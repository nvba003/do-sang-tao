<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductApi;
use App\Models\Product;
use App\Models\Branch;
use App\Models\TransactionType;
use App\Models\InventoryTransaction;
use App\Models\InventoryHistory;
use App\Models\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class InventoryTransactionController extends Controller
{
    public function showTransactions(Request $request)
    {
        $perPage = $request->input('per_page',10);
        $products = ProductApi::all(); 
        $branches = Branch::all();
        $transactionTypes = TransactionType::all();
        $query = InventoryTransaction::query()
            ->with(['inventoryHistory', 'productApi', 'user', 'transactionType'])
            ->when($request->filled('searchProductID'), function ($q) use ($request) {
                $q->where('product_id', $request->input('searchProductID'));
            })
            ->when($request->filled('search_container_code'), function ($query) use ($request) {
                $query->whereHas('containers', function ($subQuery) use ($request) {
                    $subQuery->where('container_code', $request->input('search_container_code'));
                });
            })         
            ->orderBy('updated_at', 'desc');

        $transactions = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('containers.partial_transaction_table', compact('transactions'))->render();
            $links = $transactions->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }

        return view('containers.transaction', compact('products', 'branches', 'transactionTypes', 'transactions'), ['header' => 'Nhập Xuất Thùng Hàng']);
    }

    public function fetchTransactionProduct(Request $request)
    {
        $request->validate([
            'container_code' => 'required|string|max:7',
        ]);
        $container = Container::where('container_code', $request->container_code)
                              ->with('productapi')  // Assuming there's a 'product' relationship defined in Container model
                              ->first();
        if (!$container || !$container->productapi) {
            return response()->json([
                'success' => false,
                'message' => 'Mã thùng không tồn tại hoặc không có sản phẩm liên quan.'
            ]);
        }
        return response()->json([
            'success' => true,
            'container_id' => $container->id,
            'branch_id' => $container->branch_id,
            'product_id' => $container->productapi->id,
            'productName' => $container->productapi->name,
            'sapo_quantity' => $container->productapi->inventory_quantity,
            'quantity' => $container->product_quantity
        ]);
    }

    // public function getTransactions()
    // {
    //     $transactions = InventoryTransaction::with('productapi', 'user', 'transactionType', 'inventoryHistory')
    //                 ->orderBy('updated_at', 'desc')
    //                 ->paginate(4);// Sử dụng khi chọn phân trang
    //     return response()->json(['transactions' => $transactions]);
    // }

    // public function searchContainer(Request $request)
    // {
    //     $containerId = $request->container_id;
    //     // Sắp xếp giao dịch theo ngày giảm dần và phân trang
    //     $transactions = InventoryTransaction::with(['productapi', 'user', 'transactionType', 'inventoryHistory'])
    //                     ->where('container_id', $containerId)
    //                     ->orderBy('updated_at', 'desc')
    //                     ->paginate(5); // Thêm phân trang, ví dụ 10 giao dịch mỗi trang
    //     return response()->json(['search_transactions' => $transactions]);
    //     //return response()->json($transaction);
    // }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('container-transactions.edit', compact('transaction'));
    }

    public function store(Request $request)
    {
        //dd($request)->all();
        $containerId = $request->container_id; // Lấy container_id từ request
        $quantityChange = $request->quantity; // Lấy giá trị quantity từ request
        $type = $request->type; // Lấy loại giao dịch từ request
        $transaction = InventoryTransaction::create([
            'product_id' => $request->product_id,
            'branch_id' => $request->branch_id,
            'type' => $type,
            'container_id' =>  $containerId,
            'transaction_type_id' => $request->transaction_type_id,
            'quantity' => $quantityChange,
            'user_id' => Auth::id(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        $transactionId = $transaction->id;      
        // Cập nhật số lượng sản phẩm containers table
        $container = Container::find($containerId);
        if ($container) {
            $quantityBefore = $container->product_quantity; // quantity trước khi thay đổi, lấy tại containers
            // Kiểm tra loại giao dịch và cập nhật product_quantity tương ứng
            if ($type == '1') {
                // Nếu loại giao dịch là nhập ('in'), cộng thêm quantity
                $container->product_quantity += $quantityChange;
            } elseif ($type == '2') {
                // Nếu loại giao dịch là xuất ('out'), trừ đi quantity
                $container->product_quantity -= $quantityChange;
            }
            $container->save();

            // Tạo giao dịch chi tiết, lưu tại inventory_history table
            $history = new InventoryHistory;
            $history->transaction_id = $transactionId;
            $history->quantity_before = $quantityBefore;
            $history->quantity_after = $container->product_quantity; // quantity sau khi thay đổi, đã được cập nhật ở trên
            $history->notes = $request->notes;
            $history->save();
        }

        // Quay lại trang trước đó với thông báo thành công
        return back()->with('success', 'Giao dịch được cập nhật thành công!');
    }

}
