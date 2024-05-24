<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Platform;
use App\Models\ProductApi;
use App\Models\Product;
use App\Models\Branch;
use App\Models\User;
use App\Models\Carrier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderProcess;

class OrderController extends Controller
{
    public function showOrders(Request $request)
    {
        $perPage = $request->input('per_page',15);
        // Lấy branch_id từ đường dẫn
        $branch_id = $request->route('branch_id');
        $products = ProductApi::all(); 
        $branches = Branch::all();
        $users = User::all();
        $carriers = Carrier::all();
        $stringName = 'Tiki';
        $platforms = Platform::where('name', 'like', '%' . $stringName . '%')->get();
        $query = Order::query();
            $query->where('branch_id', $branch_id)//show chi nhánh tương ứng
            // ->when($request->filled('searchOrderCode'), function ($q) use ($request) {
            //     $q->where('order_code', $request->input('searchOrderCode'));
            // })
            // ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
            //     $q->whereDate('created_at', '>=', $request->input('searchCreatedAtFrom'));
            // })
            // ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
            //     $q->whereDate('created_at', '<=', $request->input('searchCreatedAtTo'));
            // })
            // ->when($request->filled('searchCustomer'), function ($q) use ($request) {
            //     $q->where('customer_account', $request->input('searchCustomer'));
            // })
            // ->when($request->filled('order_id_check'), function ($q) use ($request) {
            //     $orderIdCheck = $request->input('order_id_check');
            //     if ($orderIdCheck == 0) {
            //         $q->whereNull('order_id');
            //     } elseif ($orderIdCheck == 1) {
            //         $q->whereNotNull('order_id');
            //     }
            // })
            // ->when($request->filled('shipping'), function ($q) use ($request) {
            //     $shipping = $request->input('shipping');
            //     if ($shipping == 0) {
            //         $q->whereNull('tracking_number');
            //     } elseif ($shipping == 1) {
            //         $q->whereNotNull('tracking_number');
            //     }
            // })
            // ->when($request->filled('status'), function ($q) use ($request) {
            //     $status = $request->input('status');
            //     if ($status !== null) {
            //         $q->where('status', $status);
            //     }
            // })
            ->with(['details.product', 'orderProcess', 'platform', 'customerAccount', 'customer', 'finances'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('orders.partial_order_table', compact('branch_id', 'orders', 'products', 'users', 'carriers', 'platforms'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('orders.order', compact('branch_id', 'products', 'branches', 'orders', 'users', 'carriers', 'platforms'), ['header' => 'Xử lý đơn hàng']);
    }
    
    public function showOrderProcesses(Request $request)
    {
        $perPage = $request->input('per_page',15);
        // Lấy branch_id từ đường dẫn
        $branch_id = $request->route('branch_id');
        $products = ProductApi::all(); 
        $branches = Branch::all();
        $users = User::all();
        $carriers = Carrier::all();
        $stringName = 'Tiki';
        $platforms = Platform::where('name', 'like', '%' . $stringName . '%')->get();
        $query = OrderProcess::query();
            $query->where('branch_id', $branch_id)//show chi nhánh tương ứng
            ->with(['order.customerAccount', 'platform', 'condition', 'carrier'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('orders.partial_order_process_table', compact('branch_id', 'orders', 'products', 'users', 'carriers', 'platforms'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('orders.order_process', compact('branch_id', 'products', 'branches', 'orders', 'users', 'carriers', 'platforms'), ['header' => 'Xử lý đơn hàng']);
    }



}
