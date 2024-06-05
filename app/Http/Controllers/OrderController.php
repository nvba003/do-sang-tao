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
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderProcess;
use App\Models\OrderType;
use App\Models\OrderStatus;
use App\Models\OrderCondition;
use App\Models\OrderFinance;
use App\Models\BundleItem;
use App\Models\Container;
use App\Models\Auxpacking\AuxpackingContainer;
use App\Models\Promotion;
use App\Models\CancelReturnReason;
use App\Models\OrderCancelAndReturn;

class OrderController extends Controller
{
    public function showOrders(Request $request, $branch_id)
    {
        $perPage = $request->input('perPage');
        $products = Product::with(['containers','bundles.product'])->get();
        // $branches = Branch::all();
        $users = User::all();
        $platforms = Platform::all();
        $carriers = Carrier::all();
        // $containers = Container::all();
        $orderTypes = OrderType::all();
        $orderStatuses = OrderStatus::all();
        $promotions = Promotion::all();
        $customers = Customer::all();
 
        // $productProcessings = OrderDetail::whereHas('order', function ($query) {
        //     $query->whereHas('orderProcess', function ($subQuery) {
        //         $subQuery->whereIn('status_id', [1, 2, 3]);
        //     });
        // })
        // ->with(['order:id,order_code']) // Tối ưu việc tải bằng cách chỉ định các trường cần thiết
        // ->get()
        // ->groupBy('product_api_id')
        // ->map(function ($details) {
        //     return $details->map(function ($detail) {
        //         return [
        //             'order_id' => $detail->order->id,
        //             'order_code' => $detail->order->order_code,
        //             'status_id' => $detail->order->orderProcess->status_id,
        //             'quantity' => $detail->quantity
        //         ];
        //     });
        // });
        
        $orderProcessingIds = OrderProcess::whereIn('status_id', [1, 2, 3])
            ->pluck('order_id');
        $productProcessings = OrderDetail::whereIn('order_id', $orderProcessingIds)
            ->with(['order:id,order_code'])
            ->get()
            ->groupBy('product_api_id')
            ->map(function ($details) {
                return $details->map(function ($detail) {
                    return [
                        'order_id' => $detail->order->id,
                        'order_code' => $detail->order->order_code,
                        // 'status_id' => $detail->order->orderProcess->status_id, // Chỉ thêm nếu bạn có quan hệ định nghĩa và cần thiết.
                        'quantity' => $detail->quantity
                    ];
                });
            });        
             
        $query = Order::where('branch_id', $branch_id)  // Lọc theo chi nhánh
            ->when($request->filled('searchOrderCode'), function ($q) use ($request) {
                $q->where('order_code', $request->input('searchOrderCode'));  // Tìm kiếm theo mã đơn hàng nếu có
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status_id', $request->input('status'));
            })
            ->when($request->filled('paymentStatus'), function ($q) use ($request) {
                $q->where('payment', $request->input('paymentStatus'));
            })
            ->when($request->filled('platform'), function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform'));
            })
            ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('searchCreatedAtFrom'));  // Lọc theo ngày tạo từ
            })
            ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('searchCreatedAtTo'));  // Lọc theo ngày tạo đến
            })
            ->when($request->filled('searchCustomer'), function ($q) use ($request) {
                $q->whereHas('customerAccount', function ($subQuery) use ($request) {
                    $subQuery->where('account_name', 'like', '%' . $request->input('searchCustomer') . '%');  // Tìm kiếm khách hàng theo tên tài khoản
                });
            })
            ->when($request->filled('packingStatus'), function ($q) use ($request) {
                if ($request->input('packingStatus') == '1') {
                    $q->has('auxpackingOrder'); // Có auxpackingOrder
                } else {
                    $q->doesntHave('auxpackingOrder'); // Không có auxpackingOrder
                }
            })
            ->when($request->filled('shipping'), function ($q) use ($request) {
                if ($request->input('shipping') == '1') {
                    $q->whereHas('orderProcess', function ($subQuery) {
                        $subQuery->whereNotNull('tracking_number');
                    });
                } else {
                    $q->whereDoesntHave('orderProcess', function ($subQuery) {
                        $subQuery->whereNotNull('tracking_number');
                    });
                }
            })
            ->with(['details.product.containers', 'details.bundles.product.containers', 'orderProcess.user', 'platform', 'customerAccount', 'customer', 'finances', 'auxpackingOrder'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            return response()->json([
                'orders' => $orders->items(),
                'links' => $orders->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
        } else {
            // For initial page load, send JSON as part of the rendered page
            $initialData = json_encode([
                'orders' => $orders->items(),
                'links' => $orders->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
            return view('orders.order', compact(
                'branch_id',
                'products',
                // 'branches',
                'users',
                'carriers',
                'platforms',
                'orderTypes',
                'orderStatuses',
                'customers',
                'productProcessings',
                // 'containers',
                'promotions',
                'initialData'),
                ['header' => 'Xử lý đơn hàng']
            );         
        }
    }
    
    public function updateOrder(Request $request)
    {
        // $validatedData = $request->validate([
        //     'order_id' => 'required|integer|exists:orders,id',
        //     'order_code' => 'required|string',
        //     'branch_id' => 'required|integer|exists:branches,id',
        //     'platform_id' => 'required|integer|exists:platforms,id',
        //     'order_type_id' => 'required|integer',
        //     'status_id' => 'required|integer',
        //     'subtotal' => 'required|numeric',
        //     'total_amount' => 'required|numeric',
        //     'tax' => 'required|numeric',
        //     'final_amount' => 'required|numeric',
        //     'details' => 'required|array',
        //     'details.*.product_api_id' => 'required|integer|exists:products,product_api_id',
        //     'details.*.quantity' => 'required|numeric',
        //     'details.*.price' => 'required|numeric'
        // ]);
        DB::beginTransaction(); // Bắt đầu transaction
        try {
            $order = Order::updateOrCreate(
                ['id' => $request->id],
                [
                    'order_code' => $request->order_code,
                    'branch_id' => $request->branch_id,
                    'platform_id' => $request->platform_id,
                    'order_type_id' => $request->order_type_id,
                    'status_id' => $request->status_id,
                    'subtotal' => $request->subtotal,
                    'total_discount' => $request->total_discount ?? 0,
                    'shipping_fee' => $request->shipping_fee ?? 0,
                    'customer_shipping_fee' => $request->customer_shipping_fee ?? 0,
                    'total_amount' => $request->total_amount,
                    'tax' => $request->tax,
                    'commission_fee' => $request->commission_fee ?? 0,
                    'final_amount' => $request->final_amount,
                    'notes' => $request->notes ?? ''
                ]
            );
            foreach ($request->details as $detail) {
                OrderDetail::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_api_id' => $detail['product_api_id']
                    ],
                    [
                        'quantity' => $detail['quantity'],
                        'price' => $detail['price'],
                        'discount_percent' => $detail['discount_percent'] ?? 0,
                        'discount' => $detail['discount'] ?? 0,
                        'total' => $detail['total'] ?? ($detail['price'] * $detail['quantity']),
                        'notes' => $detail['notes'] ?? null,
                        'bundle_id' => $detail['bundle_id'] ?? null,
                        'is_cancelled' => $detail['is_cancelled'],
                    ]
                );
            }
            DB::commit(); // Gửi tất cả các thay đổi vào cơ sở dữ liệu
            return response()->json(['message' => 'Order updated successfully!', 'order_id' => $order->id], 200);
        } catch (\Exception $e) {
            DB::rollback(); // Quay lại trạng thái trước khi bắt đầu transaction nếu có lỗi
            return response()->json(['error' => 'Failed to update order', 'message' => $e->getMessage()], 500);
        }
    }

    public function getPackingOrders()
    {
        $orders = AuxpackingContainer::select('order_id')
            ->where('status', 1)
            ->distinct()
            ->get()
            ->pluck('order_id');
        return response()->json($orders);
    }
    
    public function showOrderProcesses(Request $request, $branch_id)
    {
        $perPage = $request->input('perPage');
        $users = User::all();
        $platforms = Platform::all();
        $carriers = Carrier::all();
        $orderStatuses = OrderStatus::all();
        $orderConditions = OrderCondition::all();
        $cancelReturnReasons = CancelReturnReason::all();
        
        $query = OrderProcess::where('branch_id', $branch_id)  // Lọc theo chi nhánh
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status_id', $request->input('status'));
            })
            ->when($request->filled('platform'), function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform'));
            })
            ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
                $q->whereDate('processing_date', '>=', $request->input('searchCreatedAtFrom'));
            })
            ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
                $q->whereDate('processing_date', '<=', $request->input('searchCreatedAtTo'));
            })
            ->when($request->filled('completion'), function ($q) use ($request) {
                if ($request->input('completion') == '1') {
                    $q->whereNotNull('completion_time');  // Lọc các bản ghi có completion_time
                } elseif ($request->input('completion') == '0') {
                    $q->whereNull('completion_time');  // Lọc các bản ghi không có completion_time
                }
            })
            ->when($request->filled('return'), function ($q) use ($request) {
                $q->whereHas('cancelAndReturn') // Kiểm tra chỉ sự tồn tại của mối quan hệ cancelAndReturn
                  ->when($request->input('return') == '1', function ($q) {
                    $q->whereNotNull('received_return_date');  // Lọc các bản ghi có received_return_date
                  })
                  ->when($request->input('return') == '0', function ($q) {
                    $q->whereNull('received_return_date');  // Lọc các bản ghi không có received_return_date
                  });
            })                      
            ->when($request->filled('searchOrderCode'), function ($q) use ($request) {
                $q->whereHas('order', function ($subQuery) use ($request) {
                    $subQuery->where('order_code', $request->input('searchOrderCode'));  // Tìm kiếm theo mã đơn hàng thông qua mối quan hệ 'order'
                });
            })
            ->when($request->filled('searchCustomer'), function ($q) use ($request) {
                $q->whereHas('order.customerAccount', function ($subQuery) use ($request) {
                    $subQuery->where('account_name', 'like', '%' . $request->input('searchCustomer') . '%');  // Tìm kiếm khách hàng theo tên tài khoản thông qua mối quan hệ 'order.customerAccount'
                });
            })
            ->with(['order.customerAccount', 'platform', 'carrier', 'cancelAndReturn'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            return response()->json([
                'orders' => $orders->items(),
                'links' => $orders->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
        } else {
            $initialData = json_encode([
                'orders' => $orders->items(),
                'links' => $orders->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
            return view('orders.order_process', compact(
                'branch_id',
                'users',
                'carriers',
                'platforms',
                'orderStatuses',
                'orderConditions',
                'cancelReturnReasons',
                'initialData'),
                ['header' => 'Theo dõi đơn hàng']
            );         
        }
    }
    
    public function updateOrderCanCelReturn(Request $request)
    {
        $orderCancelAndReturn = OrderCancelAndReturn::updateOrCreate(
            ['order_id' => $request->order_id],
            [
                'type' => $request->type,
                'reason_id' => $request->reason_id,
                'carrier_return_date' => $request->carrier_return_date,
                'processed_by' => $request->processed_by,
                'notes' => $request->notes
            ]
        );
        return response()->json([
            'message' => 'Order cancellation or return saved successfully!',
            'data' => $orderCancelAndReturn
        ]);
    }
    
    public function updateOrderProcess(Request $request)
    {
        $orderProcess = OrderProcess::updateOrCreate(
            ['order_id' => $request->order_id],
            $request->only([
                'status_id', 'order_condition_id', 'responsible_user_id', 'processing_date',
                'notes', 'result', 'ship_date', 'estimated_delivery_date', 'actual_delivery_date',
                'approval_time', 'packing_time', 'delivery_handoff_time', 'completion_time', 'received_return_date'
            ])
        );
        // Update or create the OrderCancelAndReturn if data is provided
        if ($request->filled('cancel_and_return')) {
            $cancelAndReturnData = $request->input('cancel_and_return');
            $orderCancelAndReturn = OrderCancelAndReturn::updateOrCreate(
                ['order_id' => $request->order_id],
                [
                    'type' => $cancelAndReturnData['type'] ?? null,
                    'reason_id' => $cancelAndReturnData['reason_id'] ?? null,
                    'carrier_return_date' => $cancelAndReturnData['carrier_return_date'] ?? null,
                    'processed_by' => $cancelAndReturnData['processed_by'] ?? $request->user()->id, // Default to current user
                    'notes' => $cancelAndReturnData['notes'] ?? null,
                ]
            );
        }
        return response()->json([
            'message' => 'Order process and cancellation/return updated successfully!',
            'data' => [
                'orderProcess' => $orderProcess,
                'orderCancelAndReturn' => $orderCancelAndReturn ?? null // Return null if not updated or created
            ]
        ]);
    }
    
    public function updateInfoOrder(Request $request)
    {
        $order = Order::find($request->id); // Tìm đơn hàng theo ID
        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
        }
        $order->order_type_id = $request->order_type_id;
        $order->source_info = $request->source_info;
        $order->notes = $request->notes;
        $order->save();
        $orderProcess = OrderProcess::where('order_id', $request->id)->first();
        if (!$orderProcess) {
            return response()->json(['message' => 'Không tìm thấy thông tin xử lý đơn hàng'], 404);
        }
        $orderProcess->carrier_id = $request->carrier_id;
        $orderProcess->status_id = $request->status_id;
        $orderProcess->responsible_user_id = $request->responsible_user_id;
        $orderProcess->tracking_number = $request->tracking_number;
        $orderProcess->save();
        $customer = CustomerAccount::find($request->customer_account_id);
        if (!$customer) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }
        $customer->customer_id = $request->customer_id;
        $customer->save();
        return response()->json(['message' => 'Order updated successfully']);
    }
    
    public function addFinanceOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount_due' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'amount_remaining' => 'required|numeric',
        ]);
        $finance = new OrderFinance([
            'order_id' => $request->order_id,
            'amount_due' => $request->amount_due,
            'amount_paid' => $request->amount_paid,
            'amount_remaining' => $request->amount_remaining,
        ]);
        $finance->save();
        // Cập nhật trạng thái thanh toán trong bảng orders
        $order = Order::find($request->order_id);
        // Kiểm tra nếu số tiền còn lại nhỏ hơn hoặc bằng 0
        if ($request->amount_remaining <= 0) {
            $order->update(['payment' => 3]);
            return response()->json([
                'message' => 'Finance data added successfully. Payment is complete.',
                'finance' => $finance,
                'payment' => 3 //thông báo đủ
            ]);
        } else {
            $order->update(['payment' => 2]);
            return response()->json([
                'message' => 'Finance data added successfully',
                'finance' => $finance,
                'payment' => 2 //nhận thanh toán nhưng chưa đủ
            ]);
        }
    }
    
    public function getBundleItems($bundleId) {
        $items = BundleItem::where('bundle_id', $bundleId)->get();
        return response()->json($items);
    }


}
