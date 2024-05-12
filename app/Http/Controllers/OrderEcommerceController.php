<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ecommerce\OrderSendo;
use App\Models\Ecommerce\OrderSendoDetail;
use App\Models\Ecommerce\OrderShopee;
use App\Models\Ecommerce\OrderShopeeDetail;
use App\Models\Platform;
use App\Models\ProductApi;
use App\Models\Product;
use App\Models\Branch;
use App\Models\User;
use App\Models\Carrier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerAccount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderProcess;

class OrderEcommerceController extends Controller
{
    public function getPlatForms()
    {
        $platforms = Platform::select('id', 'name')->get();
        return response()->json($platforms);
    }

    public function storeOrderSendos(Request $request) //save data Sendo from extension to server
    {
        try {
            $platformId = $request->input('platform_id');
            $orders = $request->input('orders');
            // Đơn hàng chỉ lưu 1 lần, nếu có lỗi thì thêm chức năng xóa đơn để chạy lại
            foreach ($orders as $orderData) {
                $existingOrder = OrderSendo::where('order_code', $orderData['order_code'])->first(); // Kiểm tra xem order_code đã tồn tại chưa
                if (!$existingOrder) { // Nếu đơn hàng chưa tồn tại, thêm mới đơn hàng
                    $orderDate = Carbon::createFromFormat('H:i - d/m/Y', $orderData['order_date']);// Xử lý ngày giờ đặt hàng
                    $order = OrderSendo::create([
                        'order_code' => $orderData['order_code'],
                        'customer_account' => $orderData['customer_account'] ?? null,
                        'customer_phone' => $orderData['customer_phone'] ?? null,
                        'total_amount' => $orderData['total_amount'] ?? null,
                        'carrier' => $orderData['carrier'] ?? null,
                        'customer_address' => $orderData['customer_address'] ?? null,
                        'order_date' => $orderDate,
                        'platform_id' => $platformId,
                    ]);
                    foreach ($orderData['products'] as $product) {// Lưu chi tiết đơn hàng mới
                        // Lấy mã sau dấu "-" do 1 số sku có dạng "4032_85089635-G2TPMSBV0340"
                        $skuParts = explode('-', $product['sku']);
                        $sku = count($skuParts) > 1 ? $skuParts[1] : $skuParts[0];
                        $searchProduct = ProductApi::where('sku', $sku)->first();
                        $productId = $searchProduct ? $searchProduct->id : null;
                        OrderSendoDetail::create([
                            'order_sendo_id' => $order->id,
                            'sku' => $sku,
                            'product_api_id' => $productId,
                            'image' => $product['image'] ?? null,
                            'name' => $product['name'] ?? null,
                            'quantity' => $product['quantity'] ?? null,
                            //'price' => isset($product['price']) ? $product['price'] : null,
                        ]);
                    }
                }
            }
            return response()->json(['message' => 'Orders stored successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store orders', 'error' => $e->getMessage()], 500);
        }
    }

    public function showOrderSendos(Request $request)
    {
        $perPage = $request->input('per_page',10);
        // Lấy platform_id từ đường dẫn
        $platform_id = $request->route('platform_id');
        $products = ProductApi::all(); 
        $branches = Branch::all();
        $users = User::all();
        $carriers = Carrier::all();
        $stringName = 'Sendo';
        $platforms = Platform::where('name', 'like', '%' . $stringName . '%')->get();
        $query = OrderSendo::query();
            // Lọc dữ liệu dựa trên id truyền vào route
            if ($platform_id == 1) {
                $query->where('platform_id', 1);
            } elseif ($platform_id == 2) {
                $query->where('platform_id', 2);
            }
            $query->when($request->filled('searchOrderCode'), function ($q) use ($request) {
                $q->where('order_code', $request->input('searchOrderCode'));
            })
            ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('searchCreatedAtFrom'));
            })
            ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('searchCreatedAtTo'));
            })
            ->when($request->filled('searchCustomer'), function ($q) use ($request) {
                $q->where('customer_phone', $request->input('searchCustomer'));
            })
            ->when($request->filled('order_id_check'), function ($q) use ($request) {
                $orderIdCheck = $request->input('order_id_check');
                if ($orderIdCheck == 0) {
                    $q->whereNull('order_id');
                } elseif ($orderIdCheck == 1) {
                    $q->whereNotNull('order_id');
                }
            })
            ->with(['details.product', 'order.orderProcess'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('ecommerces.partial_order_sendo_table', compact('platform_id', 'orders', 'users', 'carriers', 'platforms'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('ecommerces.order_sendo', compact('platform_id', 'products', 'branches', 'orders', 'users', 'carriers', 'platforms'), ['header' => 'Đơn hàng Sendo']);
    }
    
    public function sendOrderSendos(Request $request)
    {
        try {
            $data = $request->all();
            //dd($data);
            if ($data['platform_id'] !== $data['order_ecom']['platform_id']) {//nếu thay đổi platform_id thì mới update
                $platformId = $data['platform_id'];//platformId mới
                OrderSendo::where('id', $data['order_ecom']['id'])->update([
                    'platform_id' => $platformId,
                ]);
            } else {
                $platformId = $data['order_ecom']['platform_id'];
            }
            $platform = Platform::find($platformId);
            $branchId = $platform->branch_id;
            $sourceLink = $platform->url;
            $orderSourceId = $platform->order_source_id;
            // Kiểm tra nếu order.id tồn tại | order_ecom là thông tin đơn hàng tại order_sendos, trong đó có details
            if (isset($data['order_id']) && $data['order_id']) {//$data['order_id'] là id trong orders
                // Cập nhật đơn hàng và chi tiết đơn hàng
                $order = Order::find($data['order_id']);
                //dd($order);
                if ($order) {
                    // Cập nhật đơn hàng
                    $order->update([
                        'order_code' => $data['order_ecom']['order_code'],//không cần cũng được, do code cố định
                        'branch_id' => $branchId,
                        'order_source_id' => $orderSourceId,
                        //'total_amount' => $data['order']['total_amount'] ?? null, //cố định nên không cần update
                        'source_link' => $sourceLink ? $sourceLink . $data['order_ecom']['order_code'] : null,
                        'notes' => $data['notes'] ?? null,
                    ]);
                    // Cập nhật chi tiết đơn hàng chính và đơn hàng sendo
                    foreach ($data['product_details'] as $detail) {
                        //if ($detail['product_api_id_before'] !== $detail['product_api_id']) {//nếu thay đổi product_api_id thì mới update
                        if (!empty($detail['product_api_id'])) {// Bỏ qua nếu product_api_id rỗng
                            $orderSendoDetail = OrderSendoDetail::where('id', $detail['sendo_detail_id']);
                            $orderSendoDetail->update([
                                'product_api_id' => $detail['product_api_id'],
                                'quantity' => $detail['quantity'],//bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                            ]);
                            if (empty($orderSendoDetail->order_detail_id)) {
                                $orderDetail = OrderDetail::updateOrCreate(
                                    [
                                        'order_id' => $order->id,
                                        'product_api_id' => $detail['product_api_id']
                                    ],
                                    [
                                        'quantity' => $detail['quantity'],
                                        // 'price' => $detail['price'],
                                        // 'total' => $detail['total'],
                                    ]
                                );
                                // Cập nhật OrderSendoDetail với order_detail_id mới và product_api_id mới
                                OrderSendoDetail::where('id', $detail['sendo_detail_id'])->update([
                                    'order_detail_id' => $orderDetail->id,
                                    'product_api_id' => $detail['product_api_id'], // cập nhật product_api_id mới
                                ]);
                            } else {
                                // Nếu có dữ liệu, cập nhật OrderDetail
                                OrderDetail::where('id', $orderSendoDetail->order_detail_id)->update([
                                    'product_api_id' => $detail['product_api_id'],
                                    'quantity' => $detail['quantity'], //bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                                ]);
                            }                            
                        }
                        //}
                    }
                    // Cập nhật quy trình đơn hàng
                    OrderProcess::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'responsible_user_id' => $data['responsible_user_id'],
                            'tracking_number' => $data['tracking_number'],
                            'carrier_id' => $data['carrier_id'],
                        ]
                    );
                }
            } else {
                // Tạo mới tài khoản khách hàng, mỗi đơn là mỗi tài khoản mới, không quan tâm cùng khách hay không
                $customerAccount = CustomerAccount::create([
                    'account_name' => $data['order_ecom']['customer_phone'],
                    'platform_id' => $platform->id,
                ]);
                //dd($customerAccount);
                $order = Order::create([ // Tạo mới đơn hàng
                    'order_code' => $data['order_ecom']['order_code'],
                    'customer_account_id' => $customerAccount->id,
                    'branch_id' => $branchId,
                    'order_source_id' => $orderSourceId,
                    'total_amount' => $data['order_ecom']['total_amount'] ?? null,
                    'source_link' => $sourceLink ? $sourceLink . $data['order_ecom']['order_code'] : null,
                    'notes' => $data['notes'],
                ]);

                foreach ($data['product_details'] as $detail) {
                    //dd($detail);
                    if (empty($detail['product_api_id'])) {// Bỏ qua nếu product_api_id rỗng
                        continue;
                    }
                    $orderDetail = OrderDetail::create([
                        'order_id' => $order->id,
                        'product_api_id' => $detail['product_api_id'],
                        'quantity' => $detail['quantity'],
                        // 'price' => $detail['price'],
                        // 'total' => $detail['total'],
                    ]);
                    OrderSendoDetail::where('id', $detail['sendo_detail_id'])->update([
                        'order_detail_id' => $orderDetail->id,
                        'product_api_id' => $detail['product_api_id'],//cập nhật product_api_id mới, không cần quan tâm có thay đổi
                    ]);
                }

                OrderSendo::where('id', $data['order_ecom']['id'])->update([//gắn order_id vào table order_sendos
                    'order_id' => $order->id,
                ]);

                // Tạo mới quy trình đơn hàng
                OrderProcess::create([
                    'order_id' => $order->id,
                    'status_id' => 1, //trạng thái đang xử lý
                    'responsible_user_id' => $data['responsible_user_id'],
                    'approval_time' => Carbon::now(),
                    'tracking_number' => $data['tracking_number'],
                    'carrier_id' => $data['carrier_id'],
                ]);
            }
            return response()->json(['message' => 'Order and details stored successfully', 'order_id' => $order->id], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store order and details', 'error' => $e->getMessage()], 500);
        }
    }

//====================================================================
    public function storeOrderShopees(Request $request) //save data Shopee from extension to server
    {
        try {
            $platformId = $request->input('platform_id');
            $orders = $request->input('orders');
            foreach ($orders as $orderData) {
                // $orderDate = Carbon::createFromFormat('H:i - d/m/Y', $orderData['order_date']);// Đơn Shopee không có ngày giờ đặt
                $order = OrderShopee::updateOrCreate(
                    ['order_code' => $orderData['order_code']],
                    [
                        'customer_account' => $orderData['customer_account'] ?? null,
                        // 'customer_phone' => $orderData['customer_phone'] ?? null,//không có
                        'total_amount' => $orderData['total_amount'] ?? null,
                        'carrier' => $orderData['carrier'] ?? null,
                        'tracking_number' => $orderData['tracking_number'] ?? null,
                        // 'customer_address' => $orderData['customer_address'] ?? null, //không có
                        // 'order_date' => $orderDate,
                        'status' => $orderData['status'] ?? null,
                        'notes' => $orderData['notes'] ?? null,
                        'platform_id' => $platformId,
                    ]
                );
                foreach ($orderData['products'] as $index => $product) {// Lưu chi tiết đơn hàng mới
                    $sku = $product['sku'];
                    $searchProduct = ProductApi::where('sku', $sku)->first();
                    $productId = $searchProduct ? $searchProduct->id : null;
                    OrderShopeeDetail::updateOrCreate(
                        [
                            'order_shopee_id' => $order->id,
                            'serial' => $index // Sử dụng $index làm serial
                        ],
                        [
                            'sku' => $sku,
                            'product_api_id' => $productId,
                            'image' => $product['image'] ?? null,
                            'name' => $product['name'] ?? null,
                            'quantity' => $product['quantity'] ?? null,
                            //'price' => isset($product['price']) ? $product['price'] : null,
                        ]
                    );
                }
            }
            return response()->json(['message' => 'Orders stored successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store orders', 'error' => $e->getMessage()], 500);
        }
    }
    public function showOrderShopees(Request $request)
    {
        $perPage = $request->input('per_page',10);
        // Lấy platform_id từ đường dẫn
        $platform_id = $request->route('platform_id');
        $products = ProductApi::all(); 
        $branches = Branch::all();
        $users = User::all();
        $carriers = Carrier::all();
        $stringName = 'Shopee';
        $platforms = Platform::where('name', 'like', '%' . $stringName . '%')->get();
        $query = OrderShopee::query();
            // Lọc dữ liệu dựa trên id truyền vào route
            if ($platform_id == 1) {
                $query->where('platform_id', 1);
            } elseif ($platform_id == 2) {
                $query->where('platform_id', 2);
            }
            $query->when($request->filled('searchOrderCode'), function ($q) use ($request) {
                $q->where('order_code', $request->input('searchOrderCode'));
            })
            ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('searchCreatedAtFrom'));
            })
            ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('searchCreatedAtTo'));
            })
            ->when($request->filled('searchCustomer'), function ($q) use ($request) {
                $q->where('customer_phone', $request->input('searchCustomer'));
            })
            ->when($request->filled('order_id_check'), function ($q) use ($request) {
                $orderIdCheck = $request->input('order_id_check');
                if ($orderIdCheck == 0) {
                    $q->whereNull('order_id');
                } elseif ($orderIdCheck == 1) {
                    $q->whereNotNull('order_id');
                }
            })
            ->with(['details.product', 'order.orderProcess'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('ecommerces.partial_order_shopee_table', compact('platform_id', 'orders', 'users', 'carriers', 'platforms'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('ecommerces.order_shopee', compact('platform_id', 'products', 'branches', 'orders', 'users', 'carriers', 'platforms'), ['header' => 'Đơn hàng Shopee']);
    }

}
