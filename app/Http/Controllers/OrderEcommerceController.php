<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ecommerce\OrderSendo;
use App\Models\Ecommerce\OrderSendoDetail;
use App\Models\Platform;
use Carbon\Carbon;

class OrderEcommerceController extends Controller
{
    public function getPlatForms()
    {
        $platforms = Platform::select('id', 'name')->get();
        return response()->json($platforms);
    }

    public function storeOrderSendos(Request $request)
    {
        try {
            $platformId = $request->input('platform_id');
            $orders = $request->input('orders');
            foreach ($orders as $orderData) {
                // Xử lý ngày giờ đặt hàng
                $orderDate = Carbon::createFromFormat('H:i - d/m/Y', $orderData['order_date']);
                // Kiểm tra xem order_code đã tồn tại chưa
                $order = OrderSendo::updateOrCreate(
                    ['order_code' => $orderData['order_code']],
                    [
                        'customer_account' => $orderData['customer_account'] ?? null,
                        'customer_phone' => $orderData['customer_phone'] ?? null,
                        'total_amount' => $orderData['total_amount'] ?? null,
                        'carrier' => $orderData['carrier'] ?? null,
                        'customer_address' => $orderData['customer_address'] ?? null,
                        'order_date' => $orderDate,
                        'platform_id' => $platformId,
                    ]
                );
                // Xóa chi tiết đơn hàng cũ nếu đang cập nhật đơn hàng
                OrderSendoDetail::where('order_sendo_id', $order->id)->delete();
                // Lưu chi tiết đơn hàng mới
                foreach ($orderData['products'] as $product) {
                    // Lấy mã sau dấu "-"
                    $skuParts = explode('-', $product['sku']);
                    $sku = count($skuParts) > 1 ? $skuParts[1] : $skuParts[0];
                    OrderSendoDetail::create([
                        'order_sendo_id' => $order->id,
                        'sku' => $sku,
                        'image' => $product['image'] ?? null,
                        'name' => $product['name'] ?? null,
                        'quantity' => $product['quantity'] ?? null,
                        //'price' => isset($product['price']) ? $product['price'] : null,
                    ]);
                }
            }
            return response()->json(['message' => 'Orders stored successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store orders', 'error' => $e->getMessage()], 500);
        }
    }
}
