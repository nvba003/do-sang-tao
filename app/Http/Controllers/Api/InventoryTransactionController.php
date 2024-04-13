<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Container;
use App\Models\ProductApi;

class InventoryTransactionController extends Controller
{
    public function getProduct($containerIdValue)
    {
        $container = Container::where('container_id', $containerIdValue)->first();//lấy mã thùng hàng
        if ($container) {
            $productApi = ProductApi::where('id', $container->product_id)->first();//tìm tên sản phẩm
            return response()->json([
                'status' => 'success',
                'productId' => $productApi->id,
                'productName' => $productApi->name,
                // Thêm bất kỳ thông tin sản phẩm nào bạn muốn trả về
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy mã thùng']);
        }
    }
}
