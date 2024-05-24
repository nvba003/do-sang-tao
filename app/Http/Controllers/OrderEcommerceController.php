<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ecommerce\OrderSendo;
use App\Models\Ecommerce\OrderSendoDetail;
use App\Models\Ecommerce\OrderShopee;
use App\Models\Ecommerce\OrderShopeeDetail;
use App\Models\Ecommerce\OrderLazada;
use App\Models\Ecommerce\OrderLazadaDetail;
use App\Models\Ecommerce\OrderTiki;
use App\Models\Ecommerce\OrderTikiDetail;
use App\Models\Ecommerce\OrderTiktok;
use App\Models\Ecommerce\OrderTiktokDetail;
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
            foreach ($orders as $orderData) {
                $orderDate = Carbon::createFromFormat('H:i - d/m/Y', $orderData['order_date']);// Xử lý ngày giờ đặt hàng
                // $tracking_number = $orderData['tracking_number'] ?? null;
                $order = OrderSendo::updateOrCreate(
                    ['order_code' => $orderData['order_code']],
                    [
                        'customer_account' => $orderData['customer_account'] ?? null,
                        'customer_phone' => $orderData['customer_phone'] ?? null,//không có
                        'total_amount' => $orderData['total_amount'] ?? null,
                        'carrier' => $orderData['carrier'] ?? null,
                        // 'tracking_number' => $tracking_number,
                        'customer_address' => $orderData['customer_address'] ?? null, //không có
                        'order_date' => $orderDate,
                        'status' => $orderData['status'] ?? 0,//mặc định là 0
                        'notes' => $orderData['notes'] ?? null,
                        'platform_id' => $platformId,
                    ]
                );
                // if ($tracking_number && $order->order_id) {
                //     $orderProcess = OrderProcess::where('order_id', $order->order_id)->first();
                //     if ($orderProcess) {
                //         $orderProcess->update([
                //             'tracking_number' => $tracking_number,
                //         ]);
                //     }
                // }
                foreach ($orderData['products'] as $index => $product) {// Lưu chi tiết đơn hàng mới
                    // Lấy mã sau dấu "-" do 1 số sku có dạng "4032_85089635-G2TPMSBV0340"
                    $skuParts = explode('-', $product['sku']);
                    $sku = count($skuParts) > 1 ? $skuParts[1] : $skuParts[0];
                    $searchProduct = ProductApi::where('sku', $sku)->first();
                    $productId = $searchProduct ? $searchProduct->id : null;
                    OrderSendoDetail::updateOrCreate(
                        [
                            'order_sendo_id' => $order->id,
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
                        'platform_id' => $platform->id,
                        //'total_amount' => $data['order']['total_amount'] ?? null, //cố định nên không cần update
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
                    'platform_id' => $platform->id,
                    'total_amount' => $data['order_ecom']['total_amount'] ?? null,
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
                $tracking_number = $orderData['tracking_number'] ?? null;
                
                $order = OrderShopee::updateOrCreate(
                    ['order_code' => $orderData['order_code']],
                    [
                        'customer_account' => $orderData['customer_account'] ?? null,
                        // 'customer_phone' => $orderData['customer_phone'] ?? null,//không có
                        'total_amount' => $orderData['total_amount'] ?? null,
                        'carrier' => $orderData['carrier'] ?? null,
                        'tracking_number' => $tracking_number,
                        // 'customer_address' => $orderData['customer_address'] ?? null, //không có
                        // 'order_date' => $orderDate,
                        'status' => $orderData['status'] ?? null,
                        'notes' => $orderData['notes'] ?? null,
                        'platform_id' => $platformId,
                    ]
                );
                if ($tracking_number && $order->order_id) {
                    $orderProcess = OrderProcess::where('order_id', $order->order_id)->first();
                    if ($orderProcess) {
                        $orderProcess->update([
                            'tracking_number' => $tracking_number,
                        ]);
                    }
                }
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
        $perPage = $request->input('per_page',15);
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
            if ($platform_id == 3) {
                $query->where('platform_id', 3);
            } elseif ($platform_id == 4) {
                $query->where('platform_id', 4);
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
                $q->where('customer_account', $request->input('searchCustomer'));
            })
            ->when($request->filled('order_id_check'), function ($q) use ($request) {
                $orderIdCheck = $request->input('order_id_check');
                if ($orderIdCheck == 0) {
                    $q->whereNull('order_id');
                } elseif ($orderIdCheck == 1) {
                    $q->whereNotNull('order_id');
                }
            })
            ->when($request->filled('shipping'), function ($q) use ($request) {
                $shipping = $request->input('shipping');
                if ($shipping == 0) {
                    $q->whereNull('tracking_number');
                } elseif ($shipping == 1) {
                    $q->whereNotNull('tracking_number');
                }
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->input('status');
                if ($status !== null) {
                    $q->where('status', $status);
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

    public function sendOrderShopees(Request $request)
    {
        try {
            $data = $request->all();
            //dd($data);
            if ($data['platform_id'] !== $data['order_ecom']['platform_id']) {//nếu thay đổi platform_id thì mới update
                $platformId = $data['platform_id'];//platformId mới
                OrderShopee::where('id', $data['order_ecom']['id'])->update([
                    'platform_id' => $platformId,
                ]);
            } else {
                $platformId = $data['order_ecom']['platform_id'];
            }
            $platform = Platform::find($platformId);
            $branchId = $platform->branch_id;
            // Kiểm tra nếu order.id tồn tại | order_ecom là thông tin đơn hàng tại order_shopees, trong đó có details
            if (isset($data['order_id']) && $data['order_id']) {//$data['order_id'] là id trong orders
                // Cập nhật đơn hàng và chi tiết đơn hàng
                $order = Order::find($data['order_id']);
                //dd($order);
                if ($order) {
                    // Cập nhật đơn hàng
                    $order->update([
                        'order_code' => $data['order_ecom']['order_code'],//không cần cũng được, do code cố định
                        'branch_id' => $branchId,
                        'platform_id' => $platform->id,
                        //'total_amount' => $data['order']['total_amount'] ?? null, //cố định nên không cần update
                        'notes' => $data['notes'] ?? null,
                    ]);
                    // Cập nhật chi tiết đơn hàng chính và đơn hàng shopee
                    foreach ($data['product_details'] as $detail) {
                        //if ($detail['product_api_id_before'] !== $detail['product_api_id']) {//nếu thay đổi product_api_id thì mới update
                        if (!empty($detail['product_api_id'])) {// Bỏ qua nếu product_api_id rỗng
                            $orderShopeeDetail = OrderShopeeDetail::where('id', $detail['detail_ecom_id']);
                            $orderShopeeDetail->update([
                                'product_api_id' => $detail['product_api_id'],
                                'quantity' => $detail['quantity'],//bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                            ]);
                            if (empty($orderShopeeDetail->order_detail_id)) {// nếu chưa có order_detail thì tạo mới
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
                                OrderShopeeDetail::where('id', $detail['detail_ecom_id'])->update([
                                    'order_detail_id' => $orderDetail->id,
                                    'product_api_id' => $detail['product_api_id'], // cập nhật product_api_id mới
                                ]);
                            } else {
                                // Nếu có dữ liệu, cập nhật OrderDetail
                                OrderDetail::where('id', $orderShopeeDetail->order_detail_id)->update([
                                    'product_api_id' => $detail['product_api_id'],
                                    'quantity' => $detail['quantity'], //bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                                ]);
                            }                            
                        }
                        //}
                    }
                    // Cập nhật quy trình đơn hàng
                    $tracking_number = $data['tracking_number'] ?? $data['order_ecom']['tracking_number'];
                    OrderProcess::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'responsible_user_id' => $data['responsible_user_id'],
                            'tracking_number' => $tracking_number,
                            'carrier_id' => $data['carrier_id'],
                        ]
                    );
                }
            } else {
                // Tạo mới tài khoản khách hàng, mỗi đơn là mỗi tài khoản mới, không quan tâm cùng khách hay không
                $customerAccount = CustomerAccount::create([
                    'account_name' => $data['order_ecom']['customer_account'],
                    'platform_id' => $platform->id,
                ]);
                //dd($customerAccount);
                $order = Order::create([ // Tạo mới đơn hàng
                    'order_code' => $data['order_ecom']['order_code'],
                    'customer_account_id' => $customerAccount->id,
                    'branch_id' => $branchId,
                    'platform_id' => $platform->id,
                    'total_amount' => $data['order_ecom']['total_amount'] ?? null,
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
                    OrderShopeeDetail::where('id', $detail['detail_ecom_id'])->update([
                        'order_detail_id' => $orderDetail->id,
                        'product_api_id' => $detail['product_api_id'],//cập nhật product_api_id mới, không cần quan tâm có thay đổi
                    ]);
                }

                OrderShopee::where('id', $data['order_ecom']['id'])->update([//gắn order_id vào table order_shopees
                    'order_id' => $order->id,
                ]);

                // Tạo mới quy trình đơn hàng
                $tracking_number = $data['tracking_number'] ?? $data['order_ecom']['tracking_number'];
                OrderProcess::create([
                    'order_id' => $order->id,
                    'status_id' => 1, //trạng thái đang xử lý
                    'responsible_user_id' => $data['responsible_user_id'],
                    'approval_time' => Carbon::now(),
                    'tracking_number' => $tracking_number,
                    'carrier_id' => $data['carrier_id'],
                ]);
            }
            return response()->json(['message' => 'Order and details stored successfully', 'order_id' => $order->id], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store order and details', 'error' => $e->getMessage()], 500);
        }
    }

//====================================================================
    public function storeOrderLazadas(Request $request) //save data Lazada from extension to server
    {
        try {
            $platformId = $request->input('platform_id');
            $orders = $request->input('orders');
            foreach ($orders as $orderData) {
                // $orderDateTime = Carbon::createFromFormat('d M Y H:i', $orderData['order_date']);
                // $orderDate = $orderDateTime->format('Y-m-d H:i:s');
                $orderDate = null;
                try {
                    $orderDateTime = Carbon::parse($orderData['order_date']);
                    $orderDate = $orderDateTime;
                } catch (\Exception $e) {
                    $orderDate = null;
                }

                $tracking_number = $orderData['tracking_number'] ?? null;
                
                $order = OrderLazada::updateOrCreate(
                    ['order_code' => $orderData['order_code']],
                    [
                        'customer_account' => $orderData['customer_account'] ?? null,
                        // 'customer_phone' => $orderData['customer_phone'] ?? null,//không có
                        'total_amount' => $orderData['total_amount'] ?? null,
                        'carrier' => $orderData['carrier'] ?? null,
                        'tracking_number' => $tracking_number,
                        // 'customer_address' => $orderData['customer_address'] ?? null, //không có
                        'order_date' => $orderDate,
                        'status' => $orderData['status'] ?? null,
                        'notes' => $orderData['notes'] ?? null,
                        'platform_id' => $platformId,
                    ]
                );
                if ($tracking_number && $order->order_id) {
                    $orderProcess = OrderProcess::where('order_id', $order->order_id)->first();
                    if ($orderProcess) {
                        $orderProcess->update([
                            'tracking_number' => $tracking_number,
                        ]);
                    }
                }
                foreach ($orderData['products'] as $index => $product) {// Lưu chi tiết đơn hàng mới
                    $sku = $product['sku'];
                    $searchProduct = ProductApi::where('sku', $sku)->first();
                    $productId = $searchProduct ? $searchProduct->id : null;
                    OrderLazadaDetail::updateOrCreate(
                        [
                            'order_lazada_id' => $order->id,
                            'serial' => $index // Sử dụng $index làm serial
                        ],
                        [
                            'sku' => $sku,
                            'product_api_id' => $productId,
                            'image' => $product['image'] ?? null,
                            'name' => $product['name'] ?? null,
                            'quantity' => $product['quantity'] ?? null,
                            'price' => $product['price'] ?? null,
                        ]
                    );
                }
            }
            return response()->json(['message' => 'Orders stored successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store orders', 'error' => $e->getMessage()], 500);
        }
    }

    public function showOrderLazadas(Request $request)
    {
        $perPage = $request->input('per_page',15);
        // Lấy platform_id từ đường dẫn
        $platform_id = $request->route('platform_id');
        $products = ProductApi::all(); 
        $branches = Branch::all();
        $users = User::all();
        $carriers = Carrier::all();
        $stringName = 'Lazada';
        $platforms = Platform::where('name', 'like', '%' . $stringName . '%')->get();
        $query = OrderLazada::query();
            // Lọc dữ liệu dựa trên id truyền vào route
            if ($platform_id == 5) {
                $query->where('platform_id', 5);
            } elseif ($platform_id == 6) {
                $query->where('platform_id', 6);
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
                $q->where('customer_account', $request->input('searchCustomer'));
            })
            ->when($request->filled('order_id_check'), function ($q) use ($request) {
                $orderIdCheck = $request->input('order_id_check');
                if ($orderIdCheck == 0) {
                    $q->whereNull('order_id');
                } elseif ($orderIdCheck == 1) {
                    $q->whereNotNull('order_id');
                }
            })
            ->when($request->filled('shipping'), function ($q) use ($request) {
                $shipping = $request->input('shipping');
                if ($shipping == 0) {
                    $q->whereNull('tracking_number');
                } elseif ($shipping == 1) {
                    $q->whereNotNull('tracking_number');
                }
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->input('status');
                if ($status !== null) {
                    $q->where('status', $status);
                }
            })
            ->with(['details.product', 'order.orderProcess'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('ecommerces.partial_order_lazada_table', compact('platform_id', 'orders', 'users', 'carriers', 'platforms'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('ecommerces.order_lazada', compact('platform_id', 'products', 'branches', 'orders', 'users', 'carriers', 'platforms'), ['header' => 'Đơn hàng Lazada']);
    }

    public function sendOrderLazadas(Request $request)
    {
        try {
            $data = $request->all();
            //dd($data);
            if ($data['platform_id'] !== $data['order_ecom']['platform_id']) {//nếu thay đổi platform_id thì mới update
                $platformId = $data['platform_id'];//platformId mới
                OrderLazada::where('id', $data['order_ecom']['id'])->update([
                    'platform_id' => $platformId,
                ]);
            } else {
                $platformId = $data['order_ecom']['platform_id'];
            }
            $platform = Platform::find($platformId);
            $branchId = $platform->branch_id;
            // Kiểm tra nếu order.id tồn tại | order_ecom là thông tin đơn hàng tại order_lazadas, trong đó có details
            if (isset($data['order_id']) && $data['order_id']) {//$data['order_id'] là id trong orders
                // Cập nhật đơn hàng và chi tiết đơn hàng
                $order = Order::find($data['order_id']);
                //dd($order);
                if ($order) {
                    // Cập nhật đơn hàng
                    $order->update([
                        'order_code' => $data['order_ecom']['order_code'],//không cần cũng được, do code cố định
                        'branch_id' => $branchId,
                        'platform_id' => $platform->id,
                        //'total_amount' => $data['order']['total_amount'] ?? null, //cố định nên không cần update
                        'notes' => $data['notes'] ?? null,
                    ]);
                    // Cập nhật chi tiết đơn hàng chính và đơn hàng Lazada
                    foreach ($data['product_details'] as $detail) {
                        //if ($detail['product_api_id_before'] !== $detail['product_api_id']) {//nếu thay đổi product_api_id thì mới update
                        if (!empty($detail['product_api_id'])) {// Bỏ qua nếu product_api_id rỗng
                            $orderLazadaDetail = OrderLazadaDetail::where('id', $detail['detail_ecom_id']);
                            $orderLazadaDetail->update([
                                'product_api_id' => $detail['product_api_id'],
                                'quantity' => $detail['quantity'],//bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                            ]);
                            if (empty($orderLazadaDetail->order_detail_id)) {// nếu chưa có order_detail thì tạo mới
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
                                OrderLazadaDetail::where('id', $detail['detail_ecom_id'])->update([
                                    'order_detail_id' => $orderDetail->id,
                                    'product_api_id' => $detail['product_api_id'], // cập nhật product_api_id mới
                                ]);
                            } else {
                                // Nếu có dữ liệu, cập nhật OrderDetail
                                OrderDetail::where('id', $orderLazadaDetail->order_detail_id)->update([
                                    'product_api_id' => $detail['product_api_id'],
                                    'quantity' => $detail['quantity'], //bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                                ]);
                            }                            
                        }
                        //}
                    }
                    // Cập nhật quy trình đơn hàng
                    $tracking_number = $data['tracking_number'] ?? $data['order_ecom']['tracking_number'];
                    OrderProcess::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'responsible_user_id' => $data['responsible_user_id'],
                            'tracking_number' => $tracking_number,
                            'carrier_id' => $data['carrier_id'],
                        ]
                    );
                }
            } else {
                // Tạo mới tài khoản khách hàng, mỗi đơn là mỗi tài khoản mới, không quan tâm cùng khách hay không
                $customerAccount = CustomerAccount::create([
                    'account_name' => $data['order_ecom']['customer_account'],
                    'platform_id' => $platform->id,
                ]);
                //dd($customerAccount);
                $order = Order::create([ // Tạo mới đơn hàng
                    'order_code' => $data['order_ecom']['order_code'],
                    'customer_account_id' => $customerAccount->id,
                    'branch_id' => $branchId,
                    'platform_id' => $platform->id,
                    'total_amount' => $data['order_ecom']['total_amount'] ?? null,
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
                    OrderLazadaDetail::where('id', $detail['detail_ecom_id'])->update([
                        'order_detail_id' => $orderDetail->id,
                        'product_api_id' => $detail['product_api_id'],//cập nhật product_api_id mới, không cần quan tâm có thay đổi
                    ]);
                }

                OrderLazada::where('id', $data['order_ecom']['id'])->update([//gắn order_id vào table order_lazadas
                    'order_id' => $order->id,
                ]);

                // Tạo mới quy trình đơn hàng
                $tracking_number = $data['tracking_number'] ?? $data['order_ecom']['tracking_number'];
                OrderProcess::create([
                    'order_id' => $order->id,
                    'status_id' => 1, //trạng thái đang xử lý
                    'responsible_user_id' => $data['responsible_user_id'],
                    'approval_time' => Carbon::now(),
                    'tracking_number' => $tracking_number,
                    'carrier_id' => $data['carrier_id'],
                ]);
            }
            return response()->json(['message' => 'Order and details stored successfully', 'order_id' => $order->id], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store order and details', 'error' => $e->getMessage()], 500);
        }
    }

//====================================================================
    public function storeOrderTikis(Request $request) //save data Tiki from extension to server
    {
        try {
            $platformId = $request->input('platform_id');
            $orders = $request->input('orders');
            foreach ($orders as $orderData) {
                // $tracking_number = $orderData['tracking_number'] ?? null;
                $order = OrderTiki::updateOrCreate(
                    ['order_code' => $orderData['order_code']],
                    [
                        'customer_account' => $orderData['customer_account'] ?? null,
                        // 'customer_phone' => $orderData['customer_phone'] ?? null,//không có
                        'total_amount' => $orderData['total_amount'] ?? null,
                        // 'carrier' => $orderData['carrier'] ?? null,
                        // 'tracking_number' => $tracking_number,
                        'customer_address' => $orderData['customer_address'] ?? null, //không có
                        'order_date' => $orderData['order_date'] ?? null,
                        'status' => $orderData['status'] ?? null,
                        'notes' => $orderData['notes'] ?? null,
                        'platform_id' => $platformId,
                    ]
                );
                // if ($tracking_number && $order->order_id) {
                //     $orderProcess = OrderProcess::where('order_id', $order->order_id)->first();
                //     if ($orderProcess) {
                //         $orderProcess->update([
                //             'tracking_number' => $tracking_number,
                //         ]);
                //     }
                // }
                foreach ($orderData['products'] as $index => $product) {// Lưu chi tiết đơn hàng mới
                    $sku = $product['sku'];
                    $searchProduct = ProductApi::where('sku', $sku)->first();
                    $productId = $searchProduct ? $searchProduct->id : null;
                    OrderTikiDetail::updateOrCreate(
                        [
                            'order_tiki_id' => $order->id,
                            'serial' => $index // Sử dụng $index làm serial
                        ],
                        [
                            'sku' => $sku,
                            'product_api_id' => $productId,
                            'image' => $product['image'] ?? null,
                            'name' => $product['name'] ?? null,
                            'quantity' => $product['quantity'] ?? null,
                            'price' => $product['price'] ?? null,
                        ]
                    );
                }
            }
            return response()->json(['message' => 'Orders stored successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store orders', 'error' => $e->getMessage()], 500);
        }
    }

    public function showOrderTikis(Request $request)
    {
        $perPage = $request->input('per_page',15);
        // Lấy platform_id từ đường dẫn
        $platform_id = $request->route('platform_id');
        $products = ProductApi::all(); 
        $branches = Branch::all();
        $users = User::all();
        $carriers = Carrier::all();
        $stringName = 'Tiki';
        $platforms = Platform::where('name', 'like', '%' . $stringName . '%')->get();
        $query = OrderTiki::query();
            $query->where('platform_id', 7)//platform của tiki hcm là 7
            ->when($request->filled('searchOrderCode'), function ($q) use ($request) {
                $q->where('order_code', $request->input('searchOrderCode'));
            })
            ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('searchCreatedAtFrom'));
            })
            ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('searchCreatedAtTo'));
            })
            ->when($request->filled('searchCustomer'), function ($q) use ($request) {
                $q->where('customer_account', $request->input('searchCustomer'));
            })
            ->when($request->filled('order_id_check'), function ($q) use ($request) {
                $orderIdCheck = $request->input('order_id_check');
                if ($orderIdCheck == 0) {
                    $q->whereNull('order_id');
                } elseif ($orderIdCheck == 1) {
                    $q->whereNotNull('order_id');
                }
            })
            ->when($request->filled('shipping'), function ($q) use ($request) {
                $shipping = $request->input('shipping');
                if ($shipping == 0) {
                    $q->whereNull('tracking_number');
                } elseif ($shipping == 1) {
                    $q->whereNotNull('tracking_number');
                }
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->input('status');
                if ($status !== null) {
                    $q->where('status', $status);
                }
            })
            ->with(['details.product', 'order.orderProcess'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('ecommerces.partial_order_tiki_table', compact('platform_id', 'orders', 'users', 'carriers', 'platforms'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('ecommerces.order_tiki', compact('platform_id', 'products', 'branches', 'orders', 'users', 'carriers', 'platforms'), ['header' => 'Đơn hàng Tiki']);
    }

    public function sendOrderTikis(Request $request)
    {
        try {
            $data = $request->all();
            //dd($data);
            if ($data['platform_id'] !== $data['order_ecom']['platform_id']) {//nếu thay đổi platform_id thì mới update
                $platformId = $data['platform_id'];//platformId mới
                OrderTiki::where('id', $data['order_ecom']['id'])->update([
                    'platform_id' => $platformId,
                ]);
            } else {
                $platformId = $data['order_ecom']['platform_id'];
            }
            $platform = Platform::find($platformId);
            $branchId = $platform->branch_id;
            // Kiểm tra nếu order.id tồn tại | order_ecom là thông tin đơn hàng tại order_Tikis, trong đó có details
            if (isset($data['order_id']) && $data['order_id']) {//$data['order_id'] là id trong orders
                // Cập nhật đơn hàng và chi tiết đơn hàng
                $order = Order::find($data['order_id']);
                //dd($order);
                if ($order) {
                    // Cập nhật đơn hàng
                    $order->update([
                        'order_code' => $data['order_ecom']['order_code'],//không cần cũng được, do code cố định
                        'branch_id' => $branchId,
                        'platform_id' => $platform->id,
                        //'total_amount' => $data['order']['total_amount'] ?? null, //cố định nên không cần update
                        'notes' => $data['notes'] ?? null,
                    ]);
                    // Cập nhật chi tiết đơn hàng chính và đơn hàng Tiki
                    foreach ($data['product_details'] as $detail) {
                        //if ($detail['product_api_id_before'] !== $detail['product_api_id']) {//nếu thay đổi product_api_id thì mới update
                        if (!empty($detail['product_api_id'])) {// Bỏ qua nếu product_api_id rỗng
                            $orderTikiDetail = OrderTikiDetail::where('id', $detail['detail_ecom_id']);
                            $orderTikiDetail->update([
                                'product_api_id' => $detail['product_api_id'],
                                'quantity' => $detail['quantity'],//bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                            ]);
                            if (empty($orderTikiDetail->order_detail_id)) {// nếu chưa có order_detail thì tạo mới
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
                                OrderTikiDetail::where('id', $detail['detail_ecom_id'])->update([
                                    'order_detail_id' => $orderDetail->id,
                                    'product_api_id' => $detail['product_api_id'], // cập nhật product_api_id mới
                                ]);
                            } else {
                                // Nếu có dữ liệu, cập nhật OrderDetail
                                OrderDetail::where('id', $orderTikiDetail->order_detail_id)->update([
                                    'product_api_id' => $detail['product_api_id'],
                                    'quantity' => $detail['quantity'], //bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                                ]);
                            }                            
                        }
                        //}
                    }
                    // Cập nhật quy trình đơn hàng
                    $tracking_number = $data['tracking_number'] ?? $data['order_ecom']['tracking_number'];
                    OrderProcess::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'responsible_user_id' => $data['responsible_user_id'],
                            'tracking_number' => $tracking_number,
                            'carrier_id' => $data['carrier_id'],
                        ]
                    );
                }
            } else {
                // Tạo mới tài khoản khách hàng, mỗi đơn là mỗi tài khoản mới, không quan tâm cùng khách hay không
                $customerAccount = CustomerAccount::create([
                    'account_name' => $data['order_ecom']['customer_account'],
                    'platform_id' => $platform->id,
                ]);
                //dd($customerAccount);
                $order = Order::create([ // Tạo mới đơn hàng
                    'order_code' => $data['order_ecom']['order_code'],
                    'customer_account_id' => $customerAccount->id,
                    'branch_id' => $branchId,
                    'platform_id' => $platform->id,
                    'total_amount' => $data['order_ecom']['total_amount'] ?? null,
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
                    OrderTikiDetail::where('id', $detail['detail_ecom_id'])->update([
                        'order_detail_id' => $orderDetail->id,
                        'product_api_id' => $detail['product_api_id'],//cập nhật product_api_id mới, không cần quan tâm có thay đổi
                    ]);
                }

                OrderTiki::where('id', $data['order_ecom']['id'])->update([//gắn order_id vào table order_Tikis
                    'order_id' => $order->id,
                ]);

                // Tạo mới quy trình đơn hàng
                $tracking_number = $data['tracking_number'] ?? $data['order_ecom']['tracking_number'];
                OrderProcess::create([
                    'order_id' => $order->id,
                    'status_id' => 1, //trạng thái đang xử lý
                    'responsible_user_id' => $data['responsible_user_id'],
                    'approval_time' => Carbon::now(),
                    'tracking_number' => $tracking_number,
                    'carrier_id' => $data['carrier_id'],
                ]);
            }
            return response()->json(['message' => 'Order and details stored successfully', 'order_id' => $order->id], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store order and details', 'error' => $e->getMessage()], 500);
        }
    }

//====================================================================
    public function storeOrderTiktoks(Request $request) //save data Tiktok from extension to server
    {
        try {
            $platformId = $request->input('platform_id');
            $orders = $request->input('orders');
            foreach ($orders as $orderData) {
                $orderDate = null;
                try {
                    $orderDateTime = Carbon::createFromFormat('d/m/Y H:i:s', $orderData['order_date']);
                    $orderDate = $orderDateTime; // Lưu đối tượng Carbon trực tiếp
                } catch (\Exception $e) {
                    $orderDate = null;
                }
                $tracking_number = $orderData['tracking_number'] ?? null;
                
                $order = OrderTiktok::updateOrCreate(
                    ['order_code' => $orderData['order_code']],
                    [
                        'customer_account' => $orderData['customer_account'] ?? null,
                        // 'customer_phone' => $orderData['customer_phone'] ?? null,//không có
                        'total_amount' => $orderData['total_amount'] ?? null,
                        'carrier' => $orderData['carrier'] ?? null,
                        'tracking_number' => $tracking_number,
                        // 'customer_address' => $orderData['customer_address'] ?? null, //không có
                        'order_date' => $orderDate,
                        'status' => $orderData['status'] ?? null,
                        'notes' => $orderData['notes'] ?? null,
                        'platform_id' => $platformId,
                    ]
                );
                if ($tracking_number && $order->order_id) {
                    $orderProcess = OrderProcess::where('order_id', $order->order_id)->first();
                    if ($orderProcess) {
                        $orderProcess->update([
                            'tracking_number' => $tracking_number,
                        ]);
                    }
                }
                foreach ($orderData['products'] as $index => $product) {// Lưu chi tiết đơn hàng mới
                    $sku = $product['sku'];
                    $searchProduct = ProductApi::where('sku', $sku)->first();
                    $productId = $searchProduct ? $searchProduct->id : null;
                    OrderTiktokDetail::updateOrCreate(
                        [
                            'order_tiktok_id' => $order->id,
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

    public function showOrderTiktoks(Request $request)
    {
        $perPage = $request->input('per_page',15);
        // Lấy platform_id từ đường dẫn
        $platform_id = $request->route('platform_id');
        $products = ProductApi::all(); 
        $branches = Branch::all();
        $users = User::all();
        $carriers = Carrier::all();
        $stringName = 'Tiktok';
        $platforms = Platform::where('name', 'like', '%' . $stringName . '%')->get();
        $query = OrderTiktok::query();
            $query->where('platform_id', 8)//tiktok hcm có platform là 8
            ->when($request->filled('searchOrderCode'), function ($q) use ($request) {
                $q->where('order_code', $request->input('searchOrderCode'));
            })
            ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('searchCreatedAtFrom'));
            })
            ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('searchCreatedAtTo'));
            })
            ->when($request->filled('searchCustomer'), function ($q) use ($request) {
                $q->where('customer_account', $request->input('searchCustomer'));
            })
            ->when($request->filled('order_id_check'), function ($q) use ($request) {
                $orderIdCheck = $request->input('order_id_check');
                if ($orderIdCheck == 0) {
                    $q->whereNull('order_id');
                } elseif ($orderIdCheck == 1) {
                    $q->whereNotNull('order_id');
                }
            })
            ->when($request->filled('shipping'), function ($q) use ($request) {
                $shipping = $request->input('shipping');
                if ($shipping == 0) {
                    $q->whereNull('tracking_number');
                } elseif ($shipping == 1) {
                    $q->whereNotNull('tracking_number');
                }
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->input('status');
                if ($status !== null) {
                    $q->where('status', $status);
                }
            })
            ->with(['details.product', 'order.orderProcess'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('ecommerces.partial_order_tiktok_table', compact('platform_id', 'orders', 'users', 'carriers', 'platforms'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('ecommerces.order_tiktok', compact('platform_id', 'products', 'branches', 'orders', 'users', 'carriers', 'platforms'), ['header' => 'Đơn hàng Tiktok']);
    }

    public function sendOrderTiktoks(Request $request)
    {
        try {
            $data = $request->all();
            //dd($data);
            if ($data['platform_id'] !== $data['order_ecom']['platform_id']) {//nếu thay đổi platform_id thì mới update
                $platformId = $data['platform_id'];//platformId mới
                OrderTiktok::where('id', $data['order_ecom']['id'])->update([
                    'platform_id' => $platformId,
                ]);
            } else {
                $platformId = $data['order_ecom']['platform_id'];
            }
            $platform = Platform::find($platformId);
            $branchId = $platform->branch_id;
            // Kiểm tra nếu order.id tồn tại | order_ecom là thông tin đơn hàng tại order_Tiktoks, trong đó có details
            if (isset($data['order_id']) && $data['order_id']) {//$data['order_id'] là id trong orders
                // Cập nhật đơn hàng và chi tiết đơn hàng
                $order = Order::find($data['order_id']);
                //dd($order);
                if ($order) {
                    // Cập nhật đơn hàng
                    $order->update([
                        'order_code' => $data['order_ecom']['order_code'],//không cần cũng được, do code cố định
                        'branch_id' => $branchId,
                        'platform_id' => $platform->id,
                        //'total_amount' => $data['order']['total_amount'] ?? null, //cố định nên không cần update
                        'notes' => $data['notes'] ?? null,
                    ]);
                    // Cập nhật chi tiết đơn hàng chính và đơn hàng Tiktok
                    foreach ($data['product_details'] as $detail) {
                        //if ($detail['product_api_id_before'] !== $detail['product_api_id']) {//nếu thay đổi product_api_id thì mới update
                        if (!empty($detail['product_api_id'])) {// Bỏ qua nếu product_api_id rỗng
                            $orderTiktokDetail = OrderTiktokDetail::where('id', $detail['detail_ecom_id']);
                            $orderTiktokDetail->update([
                                'product_api_id' => $detail['product_api_id'],
                                'quantity' => $detail['quantity'],//bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                            ]);
                            if (empty($orderTiktokDetail->order_detail_id)) {// nếu chưa có order_detail thì tạo mới
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
                                OrderTiktokDetail::where('id', $detail['detail_ecom_id'])->update([
                                    'order_detail_id' => $orderDetail->id,
                                    'product_api_id' => $detail['product_api_id'], // cập nhật product_api_id mới
                                ]);
                            } else {
                                // Nếu có dữ liệu, cập nhật OrderDetail
                                OrderDetail::where('id', $orderShopeeDetail->order_detail_id)->update([
                                    'product_api_id' => $detail['product_api_id'],
                                    'quantity' => $detail['quantity'], //bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                                ]);
                            }                            
                        }
                        //}
                    }
                    // Cập nhật quy trình đơn hàng
                    $tracking_number = $data['tracking_number'] ?? $data['order_ecom']['tracking_number'];
                    OrderProcess::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'responsible_user_id' => $data['responsible_user_id'],
                            'tracking_number' => $tracking_number,
                            'carrier_id' => $data['carrier_id'],
                        ]
                    );
                }
            } else {
                // Tạo mới tài khoản khách hàng, mỗi đơn là mỗi tài khoản mới, không quan tâm cùng khách hay không
                $customerAccount = CustomerAccount::create([
                    'account_name' => $data['order_ecom']['customer_account'],
                    'platform_id' => $platform->id,
                ]);
                //dd($customerAccount);
                $order = Order::create([ // Tạo mới đơn hàng
                    'order_code' => $data['order_ecom']['order_code'],
                    'customer_account_id' => $customerAccount->id,
                    'branch_id' => $branchId,
                    'platform_id' => $platform->id,
                    'total_amount' => $data['order_ecom']['total_amount'] ?? null,
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
                    OrderTiktokDetail::where('id', $detail['detail_ecom_id'])->update([
                        'order_detail_id' => $orderDetail->id,
                        'product_api_id' => $detail['product_api_id'],//cập nhật product_api_id mới, không cần quan tâm có thay đổi
                    ]);
                }

                OrderTiktok::where('id', $data['order_ecom']['id'])->update([//gắn order_id vào table order_Tiktoks
                    'order_id' => $order->id,
                ]);

                // Tạo mới quy trình đơn hàng
                $tracking_number = $data['tracking_number'] ?? $data['order_ecom']['tracking_number'];
                OrderProcess::create([
                    'order_id' => $order->id,
                    'status_id' => 1, //trạng thái đang xử lý
                    'responsible_user_id' => $data['responsible_user_id'],
                    'approval_time' => Carbon::now(),
                    'tracking_number' => $tracking_number,
                    'carrier_id' => $data['carrier_id'],
                ]);
            }
            return response()->json(['message' => 'Order and details stored successfully', 'order_id' => $order->id], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store order and details', 'error' => $e->getMessage()], 500);
        }
    }

//====================================================================
    // public function storeOrderSapo(Request $request)
    // {
    //     $platformId = $request->input('platform_id');
    //     $platform = Platform::find($platformId);
    //     $branchId = $platform->branch_id;
    //     $data = $request->input('orders');

    //     $customerAccount = CustomerAccount::create([
    //         'account_name' => $data['customerName'] . " - " . $data['customerPhone'],
    //         'platform_id' => $platform->id,
    //     ]);
    //     // Tạo đơn hàng mới
    //     $order = new Order;
    //     $order->order_code = $data['orderID'];
    //     $order->branch_id = $branchId;
    //     $order->platform_id = $platformId;
    //     $order->customer_account_id = $customerAccount->id;
    //     $order->total_discount = (int) str_replace(',', '', $data['totalDiscount']);
    //     $order->total_amount = (int) str_replace(',', '', $data['totalAmount']);
    //     $order->customer_shipping_fee = (int) str_replace(',', '', $data['shippingFee']);
    //     $order->notes = $data['note'];
    //     $order->save();

    //     foreach ($data['products'] as $product) {
    //         $productItem = Product::find($product['sku']);

    //         $detail = new OrderDetail;
    //         $detail->order_id = $order->id;
    //         $detail->product_api_id = $productItem->product_api_id;
    //         $detail->bundle_id = $productItem->bundle_id;
    //         $detail->quantity = $product['quantity'];
    //         $detail->price = (int) str_replace(',', '', $product['unitPrice']);
    //         $detail->discount = (int) str_replace(',', '', $product['discount']);
    //         $detail->total = (int) str_replace(',', '', $product['totalPrice']);
    //         $detail->save();
    //     }

    //     return response()->json([
    //         'message' => 'Order created successfully!',
    //         'order_id' => $order->id
    //     ]);
    // }
    public function storeOrderSapo(Request $request)
    {
        $platformId = $request->input('platform_id');
        $platform = Platform::find($platformId);
        $branchId = $platform->branch_id;
        $data = $request->input('orders');
        $order = Order::where('order_code', $data['orderID'])->first();
        if (!$order) {// Nếu đơn hàng không tồn tại
            $customerAccount = CustomerAccount::create([
                'account_name' => $data['customerName'] . " - " . $data['customerPhone'],
                'platform_id' => $platform->id,
            ]);
            $order = new Order;
            $order->order_code = $data['orderID'];
            $order->branch_id = $branchId;
            $order->platform_id = $platformId;
            $order->customer_account_id = $customerAccount->id;
            $order->total_discount = (int) str_replace(',', '', $data['totalDiscount']);
            $order->total_amount = (int) str_replace(',', '', $data['totalAmount']);
            $order->customer_shipping_fee = (int) str_replace(',', '', $data['shippingFee']);
            $order->notes = $data['note'];
            $order->save();
        } else {
            // Cập nhật đơn hàng nếu đã tồn tại
            $order->branch_id = $branchId;
            $order->platform_id = $platformId;
            $order->total_discount = (int) str_replace(',', '', $data['totalDiscount']);
            $order->total_amount = (int) str_replace(',', '', $data['totalAmount']);
            $order->customer_shipping_fee = (int) str_replace(',', '', $data['shippingFee']);
            $order->notes = $data['note'];
            $order->save();
            CustomerAccount::where('id', $order->customer_account_id)->update([
                'account_name' => $data['customerName'] . " - " . $data['customerPhone'],
                'platform_id' => $platform->id,
            ]);
        }
        // Xử lý sản phẩm trong đơn hàng
        foreach ($data['products'] as $product) {
            $productItem = Product::find($product['sku']); // Giả sử bạn đã có SKU là unique key để tìm kiếm

            $detail = new OrderDetail;
            $detail->order_id = $order->id;
            $detail->product_api_id = $productItem->product_api_id;
            $detail->bundle_id = $productItem->bundle_id;
            $detail->quantity = $product['quantity'];
            $detail->price = (int) str_replace(',', '', $product['unitPrice']);
            $detail->discount = (int) str_replace(',', '', $product['discount']);
            $detail->total = (int) str_replace(',', '', $product['totalPrice']);
            $detail->save();
        }

        return response()->json([
            'message' => 'Order processed successfully!',
            'order_id' => $order->id
        ]);
    }


}
