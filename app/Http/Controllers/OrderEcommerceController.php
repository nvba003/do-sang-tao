<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ecommerce\OrderSendo;
use App\Models\Ecommerce\OrderSendoDetail;
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

    public function showOrderSendoCTs(Request $request)
    {
        $perPage = $request->input('per_page',10);
        $products = ProductApi::all(); 
        $branches = Branch::all();
        $users = User::all();
        $carriers = Carrier::all();
        $stringName = 'Sendo';
        $platforms = Platform::where('name', 'like', '%' . $stringName . '%')->get();
        $query = OrderSendo::query()
            ->when($request->filled('searchOrderCode'), function ($q) use ($request) {
                $q->where('order_code', $request->input('searchOrderCode'));
            })
            ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('searchCreatedAtFrom'));
            })
            ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('searchCreatedAtTo'));
            })
            ->with(['details.product', 'order.orderProcess'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('ecommerces.partial_order_sendo_table', compact('orders', 'users', 'carriers', 'platforms'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('ecommerces.order_sendo', compact('products', 'branches', 'orders', 'users', 'carriers', 'platforms'), ['header' => 'Đơn hàng Sendo']);
    }
    
    public function sendOrderSendos(Request $request)
    {
        try {
            $data = $request->all();
            //dd($data);
            if ($data['platform_id'] !== $data['order']['platform_id']) {//nếu thay đổi platform_id thì mới update
                $platformId = $data['platform_id'];//platformId mới
                OrderSendo::where('id', $data['order']['id'])->update([
                    'platform_id' => $platformId,
                ]);
            } else {
                $platformId = $data['order']['platform_id'];
            }
            $platform = Platform::find($platformId);
            $branchId = $platform->branch_id;
            $sourceLink = $platform->url;
            $orderSourceId = $platform->order_source_id;
            // Kiểm tra nếu order.id tồn tại | order là thông tin đơn hàng tại order_sendos, trong đó có details
            if (isset($data['order']['order_id']) && $data['order']['order_id']) {
                // Cập nhật đơn hàng và chi tiết đơn hàng
                $order = Order::find($data['order']['order_id']);
                //dd($order);
                if ($order) {
                    // Cập nhật đơn hàng
                    $order->update([
                        'order_code' => $data['order']['order_code'],//không cần cũng được, do code cố định
                        'branch_id' => $branchId,
                        'order_source_id' => $orderSourceId,
                        //'total_amount' => $data['order']['total_amount'] ?? null, //cố định nên không cần update
                        'source_link' => $sourceLink ? $sourceLink . $data['order']['order_code'] : null,
                        'notes' => $data['notes'] ?? null,
                    ]);
                    // Cập nhật chi tiết đơn hàng chính và đơn hàng sendo
                    foreach ($data['product_details'] as $detail) {
                        if ($detail['product_api_id_before'] !== $detail['product_api_id']) {//nếu thay đổi product_api_id thì mới update
                            $orderSendoDetail = OrderSendoDetail::where('id', $detail['sendo_detail_id']);
                            $orderSendoDetail->update([
                                'product_api_id' => $detail['product_api_id'],
                                'quantity' => $detail['quantity'],//bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                            ]);
                            OrderDetail::where('id', $orderSendoDetail->order_detail_id)->update([
                                'product_api_id' => $detail['product_api_id'],
                                'quantity' => $detail['quantity'],//bỏ qua cũng được do chưa định làm chức năng thay đổi số lượng
                            ]);
                        }
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
                    'account_name' => $data['order']['customer_phone'],
                    'platform_id' => $platform->id,
                ]);
                //dd($customerAccount);
                $order = Order::create([ // Tạo mới đơn hàng
                    'order_code' => $data['order']['order_code'],
                    'customer_account_id' => $customerAccount->id,
                    'branch_id' => $branchId,
                    'order_source_id' => $orderSourceId,
                    'total_amount' => $data['order']['total_amount'] ?? null,
                    'source_link' => $sourceLink ? $sourceLink . $data['order']['order_code'] : null,
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

                OrderSendo::where('id', $data['order']['id'])->update([//gắn order_id vào table order_sendos
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

}
