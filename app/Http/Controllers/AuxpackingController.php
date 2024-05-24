<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auxpacking\AuxpackingOrder;
use App\Models\Auxpacking\AuxpackingProduct;
use App\Models\Auxpacking\AuxpackingProductSummary;
use App\Models\Auxpacking\AuxpackingContainer;
use App\Models\Auxpacking\AuxpackingScan;
use App\Models\Auxpacking\AuxpackingLog;
use App\Models\Platform;
use App\Models\ProductApi;
use App\Models\Product;
use App\Models\Branch;
use App\Models\User;
use App\Models\Carrier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderProcess;
use App\Models\Container;

class AuxpackingController extends Controller
{
    public function updateProductSummaries()
    {
        // Lấy tất cả sản phẩm và tổng hợp số liệu
        $products = AuxpackingProduct::all();

        // Tạo hoặc cập nhật tổng hợp sản phẩm
        foreach ($products as $product) {
            $summary = AuxpackingProductSummary::updateOrCreate(
                ['product_api_id' => $product->product_api_id],
                [
                    'total_quantity' => AuxpackingProduct::where('product_api_id', $product->product_api_id)->sum('quantity'),
                    'quantity_retrieved' => 0, // Bạn cần cập nhật logic tương ứng để tính số lượng đã lấy từ kho
                    'quantity_delivered' => 0, // Bạn cần cập nhật logic tương ứng để tính số lượng đã giao
                ]
            );

            // Cập nhật các trường khác tùy theo nhu cầu
        }

        return response()->json(['message' => 'Product summaries updated successfully!']);
    }

    public function showProducts(Request $request)
    {
        $perPage = $request->input('per_page',15);
        // Lấy branch_id từ đường dẫn
        $branch_id = $request->route('branch_id');
        $productApis = ProductApi::all(); 
        $branches = Branch::all();
        $allContainers = Container::all();
        $users = User::all();
        $query = AuxpackingProduct::query();
            $query->where('branch_id', $branch_id)//show chi nhánh tương ứng
            ->with(['containers.container.location.parent', 'productApi', 'order.platform'])
            ->orderBy('created_at', 'desc');
        $allProducts = $query->paginate($perPage);
        $products = []; //productsGrouped
        // Nhóm sản phẩm theo product_api_id và sau đó nhóm container theo container_id
        $productGroups = $allProducts->groupBy('product_api_id');
        foreach ($productGroups as $productApiId => $productGroup) {
            $containersGrouped = [];
            foreach ($productGroup as $product) {
                foreach ($product->containers as $container) {
                    // Thêm thông tin cần thiết vào mỗi container
                    $containerData = $container->toArray();
                    $containerData['auxpacking_product'] = $product;
                    $containerData['product_api'] = $product->productApi;
                    $containerData['order'] = $product->order;
                    $containersGrouped[$container->container_id][] = $containerData;
                }
            }
            $products[$productApiId] = $containersGrouped;
        }
        if ($request->ajax()) {
            $view = view('auxpackings.partial_auxpacking_product_table', compact('branch_id', 'products', 'users', 'allContainers'))->render();
            $links = $products->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('auxpackings.auxpacking_product', compact('branch_id', 'products', 'branches', 'users', 'productGroups', 'allContainers'), ['header' => 'Tổng hợp sản phẩm đóng gói']);
    }

    public function updateContainer(Request $request)
    {
        $containerId = $request->containerId;
        $isSelected = $request->isSelected;

        // Xử lý dữ liệu, ví dụ: lưu vào cơ sở dữ liệu
        $container = AuxpackingContainer::find($containerId);
        if ($container) {
            $container->status = $isSelected;
            $container->save();
            return response()->json(['message' => 'Cập nhật container thành công']);
        } else {
            return response()->json(['message' => 'Không tìm thấy container'], 404);
        }
    }

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
        $query = AuxpackingOrder::query();
            $query->where('branch_id', $branch_id)//show chi nhánh tương ứng
            // ->when($request->filled('status'), function ($q) use ($request) {
            //     $q->where('status', $request->input('status'));
            // })
            ->with(['products.containers.container.location.parent', 'products.productApi', 'order.platform', 'order.customerAccount'])
            ->orderBy('created_at', 'desc');
        $orders = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('auxpackings.partial_auxpacking_order_table', compact('branch_id', 'products', 'users', 'carriers', 'platforms'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('auxpackings.auxpacking_order', compact('branch_id', 'orders', 'branches', 'users', 'carriers', 'platforms'), ['header' => 'Tổng hợp đơn hàng đóng gói']);
    }

    public function showContainers(Request $request)
    {
        $perPage = $request->input('per_page',15);
        // Lấy branch_id từ đường dẫn
        $branch_id = $request->route('branch_id');
        $products = ProductApi::all(); 
        $users = User::all();
        $query = AuxpackingContainer::query();
            $query->where('branch_id', $branch_id)//show chi nhánh tương ứng
            ->with(['product', 'productApi', 'order.platform', 'container.location.parent'])
            ->orderBy('created_at', 'desc');
        $allContainers = $query->paginate($perPage);
        $containers = $allContainers->groupBy('container_id');
        if ($request->ajax()) {
            $view = view('auxpackings.partial_auxpacking_container_table', compact('branch_id', 'containers', 'users'))->render();
            $links = $containers->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('auxpackings.auxpacking_container', compact('branch_id', 'containers', 'users'), ['header' => 'Tổng hợp thùng hàng đóng gói']);
    }

    public function showScans(Request $request)
    {
        $perPage = $request->input('per_page',15);
        // Lấy branch_id từ đường dẫn
        $branch_id = $request->route('branch_id');
        $products = ProductApi::all(); 
        $users = User::all();
        $query = AuxpackingScan::query();
            $query->where('branch_id', $branch_id)//show chi nhánh tương ứng
            ->with(['order'])
            ->orderBy('created_at', 'desc');
        $scans = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('auxpackings.partial_auxpacking_scan_table', compact('branch_id', 'scans', 'users'))->render();
            $links = $scans->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        return view('auxpackings.auxpacking_scan', compact('branch_id', 'scans', 'users'), ['header' => 'Quét đơn']);
    }
    
    public function storeScan(Request $request)
    {
        $tracking_number = $request->tracking_number;
        $orderProcess = OrderProcess::where('tracking_number', $tracking_number)->first();
        if (!$orderProcess) {
            return response()->json([
                'message' => 'Không có đơn hàng này',
                'status' => 404
            ], 404);
        }
        $order = Order::find($orderProcess->order_id);
        if (!$order) {
            return response()->json([
                'message' => 'Không có thông tin đơn hàng',
                'status' => 404
            ], 404);
        }
        // Kiểm tra xem đơn hàng đã được quét chưa
        $existingScan = AuxpackingScan::where('tracking_number', $tracking_number)->first();
        if ($existingScan) {
            return response()->json([
                'message' => 'Mã vận đơn đã tồn tại',
                'status' => 409
            ], 409); // Conflict
        }
        $scan = new AuxpackingScan();
        $scan->order_id = $order->id;
        $scan->branch_id = $order->branch_id;
        $scan->platform_id = $order->platform_id;
        $scan->user_id = Auth::id(); // Người dùng hiện tại
        $scan->tracking_number = $tracking_number;
        $scan->created_at = now();
        $scan->save();
        return response()->json([
            'message' => 'Đã lưu!',
            'data' => $scan,
            'status' => 200
        ], 200);
    }

}
