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
    public function addPackings(Request $request)
    {
        // Validate request data
        // $data = $request->validate([
        //     'order_id' => 'required|integer|exists:orders,id',
        //     'branch_id' => 'nullable|integer|exists:branches,id',
        //     'platform_id' => 'nullable|integer|exists:platforms,id',
        //     'products' => 'required|array',
        //     'products.*.product_api_id' => 'required|integer|exists:products,product_api_id',
        //     'products.*.quantity' => 'required|numeric|min:0',
        //     'products.*.notes' => 'nullable|string',
        //     'products.*.containers' => 'required|array',
        //     'products.*.containers.*.container_code' => 'required|string',
        //     'products.*.containers.*.product_quantity' => 'required|numeric',
        //     'products.*.bundle_id' => 'nullable|integer|exists:bundles,id',
        // ]);

        $auxpackingOrder = AuxpackingOrder::updateOrCreate(
            ['order_id' => $request->order_id],
            [
                'branch_id' => $request->branch_id,
                'platform_id' => $request->platform_id,
                'status' => 1, // Default status
                'notes' => $request->notes ?? null,
            ]
        );
        // Loop through each product and store them
        foreach ($request->products as $product_api_id => $product) {
            $auxpackingProduct = AuxpackingProduct::updateOrCreate(
                [
                    'auxpacking_order_id' => $auxpackingOrder->id,
                    'product_api_id' => $product_api_id // Use the key as product_api_id
                ],
                [
                    'branch_id' => $request->branch_id,
                    'platform_id' => $request->platform_id,
                    'bundle_id' => $product['bundle_id'] ?? null,
                    'quantity' => $product['quantity'],
                    'status' => 1, // Default status
                    'notes' => $product['notes'] ?? null,
                ]
            );
            // Process each container for the current product
            foreach ($product['containers'] as $container) {
                $filteredContainers = $this->filterAndSortContainers($product['containers'], $product['quantity']);
            
                foreach ($filteredContainers as $container) {
                    AuxpackingContainer::updateOrCreate(
                        [
                            'order_id' => $request->order_id,
                            'auxpacking_product_id' => $auxpackingProduct->id,
                            'product_api_id' => $product_api_id,
                            'container_id' => $container['container_id'],
                        ],
                        [
                            'branch_id' => $request->branch_id,
                            'platform_id' => $request->platform_id,
                            'quantity' => $container['product_quantity'], // Sử dụng số lượng đã được tính toán
                            'status' => false, // Default status as not retrieved
                            'notes' => $container['notes'] ?? null
                        ]
                    );
                }
            }            
            
        }
        

        return response()->json([
            'message' => 'Packing details stored successfully!',
            'auxpackingOrder' => $auxpackingOrder //->load('products') // Eager load associated products if needed
        ]);
    }

    private function filterAndSortContainers($containers, $requiredQuantity) {
        $sortedContainers = collect($containers)->sortByDesc(function ($container) {
            return intval(substr($container['container_code'], -1));
        })->filter(function ($container) {
            return floatval($container['product_quantity']) > 0;
        });
    
        $selectedContainers = [];
        $totalQuantity = 0;
    
        foreach ($sortedContainers as $container) {
            $currentAvailable = floatval($container['product_quantity']);
            if ($totalQuantity < $requiredQuantity) {
                $needed = min($currentAvailable, $requiredQuantity - $totalQuantity);
                $selectedContainers[] = [
                    'container_id' => $container['container_id'],
                    'container_code' => $container['container_code'],
                    'product_quantity' => $needed // Số lượng thực sự cần lấy
                ];
                $totalQuantity += $needed;
            }
        }
    
        return $selectedContainers;
    }    

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
        // $perPage = $request->input('per_page',15); // Không phân trang
        // Lấy branch_id từ đường dẫn
        $branch_id = $request->route('branch_id');
        $allProducts = Product::all(); 
        $branches = Branch::all();
        // $allContainers = Container::all();
        $allContainers = Container::select('id', 'container_code', 'location_id', 'product_id', 'product_quantity', 'branch_id')
        ->where('branch_id', $branch_id)
        ->get();
        $users = User::all();
        $platforms = Platform::all();
        $query = AuxpackingProduct::query();
            $query->where('branch_id', $branch_id)//show chi nhánh tương ứng
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->input('status'));
            })
            ->when($request->filled('platform'), function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform'));
            })
            ->when($request->filled('searchProductCode'), function ($q) use ($request) {
                $q->where('product_api_id', $request->input('searchProductCode'));
            })
            ->with(['containers.container.location.parent', 'productApi', 'order.platform'])
            ->orderBy('created_at', 'desc');
        $queryProducts = $query->get();//->paginate($perPage);
        $products = []; //productsGrouped
        // Nhóm sản phẩm theo product_api_id và sau đó nhóm container theo container_id
        $productGroups = $queryProducts->groupBy('product_api_id');
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
            // Chỉ thêm $containersGrouped vào $products nếu nó không rỗng
            if (!empty($containersGrouped)) {
                $products[$productApiId] = $containersGrouped;
            }
        }
        if ($request->ajax()) {
            // $view = view('auxpackings.partial_auxpacking_product_table', compact('branch_id', 'products', 'users', 'allContainers', 'platforms'))->render();
            // $links = $products->links()->toHtml();
            // return response()->json(['table' => $view, 'links' => $links]);
            return response()->json(['products' => $products]);
        }
        return view('auxpackings.auxpacking_product', compact('branch_id', 'products', 'branches', 'users', 'productGroups', 'allContainers', 'platforms', 'allProducts'), ['header' => 'Tổng hợp sản phẩm đóng gói']);
    }

    public function updateContainer(Request $request)
    {
        $containerId = $request->containerId;
        $isSelected = $request->isSelected;
        $quantity = $request->quantity;
        // Xử lý dữ liệu, ví dụ: lưu vào cơ sở dữ liệu
        $container = AuxpackingContainer::find($containerId);
        if ($container) {
            $container->status = $isSelected;
            $container->quantity = $quantity;
            $container->save();
            // Gọi function để cập nhật trạng thái của sản phẩm
            $auxpackingProduct = $this->updateAuxpackingProductStatus($container->auxpacking_product_id);
            return response()->json([
                'message' => 'Cập nhật container thành công',
                'auxpackingProduct' => $auxpackingProduct,
                'auxpackingContainer' => $container
            ]);
        } else {
            return response()->json(['message' => 'Không tìm thấy container'], 404);
        }
    }

    function updateAuxpackingProductStatus($auxpackingProductId)
    {
        $auxpackingProduct = AuxpackingProduct::find($auxpackingProductId);
        if (!$auxpackingProduct) {
            return null;
        }
        // Tính tổng số lượng của tất cả container có cùng auxpacking_product_id
        $totalQuantity = AuxpackingContainer::where('auxpacking_product_id', $auxpackingProductId)
                        ->where('product_api_id', $auxpackingProduct->product_api_id)
                        ->where('status', 1)
                        ->sum('quantity');
        // Cập nhật trạng thái dựa trên tổng số lượng và số lượng định mức của sản phẩm
        if ($totalQuantity == 0) {
            $auxpackingProduct->status = 1; // Chưa lấy
        } elseif ($totalQuantity > 0 && $totalQuantity < $auxpackingProduct->quantity) {
            $auxpackingProduct->status = 2; // lấy chưa đủ
        } elseif ($totalQuantity >= $auxpackingProduct->quantity) {
            $auxpackingProduct->status = 3; // Đã lấy đủ
        }
        $auxpackingProduct->save();
        return $auxpackingProduct;
    }

    public function addContainer(Request $request)
    {
        // Xác thực dữ liệu đầu vào (tuỳ chọn)
        $validatedData = $request->validate([
            'branch_id' => 'required|integer',
            'platform_id' => 'required|integer',
            'order_id' => 'required|integer',
            'auxpacking_product_id' => 'required|integer',
            'product_api_id' => 'required|integer',
            'container_id' => 'required|integer',
            'quantity' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);
        // Tạo một record mới trong cơ sở dữ liệu
        $auxpackingContainer = new AuxpackingContainer($validatedData);
        $auxpackingContainer->save();
        return response()->json([
            'message' => 'Container successfully saved',
            'data' => $auxpackingContainer
        ], 201);
    }
    
    public function removeContainer(Request $request)
    {
        $data = $request->validate([
            'containerId' => 'required|integer'
        ]);
        $containerId = $data['containerId'];
        try {
            $container = AuxpackingContainer::findOrFail($containerId);
            // $auxpackingProduct = $this->updateAuxpackingProductStatus($container->auxpacking_product_id);
            $container->delete();
            return response()->json([
                'message' => 'Xóa container thành công',
                // 'auxpackingProduct' => $auxpackingProduct
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Container not found.'], 404);
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
        // $allContainers = Container::all();
        $allContainers = Container::select('id', 'container_code', 'location_id', 'product_id', 'product_quantity', 'branch_id')
        ->where('branch_id', $branch_id)
        ->get();
        $carriers = Carrier::all();
        $platforms = Platform::all();
        $query = AuxpackingOrder::query();
            $query->where('branch_id', $branch_id)//show chi nhánh tương ứng
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->input('status'));
            })
            ->when($request->filled('platform'), function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform'));
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
            ->with(['products.containers.container.location.parent', 'products.productApi', 'order.platform', 'order.customerAccount'])
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
            return view('auxpackings.auxpacking_order', compact(
                'branch_id',
                'orders',
                'branches',
                'users',
                'carriers',
                'platforms',
                'allContainers',
                'initialData'),
                ['header' => 'Tổng hợp đơn hàng đóng gói']
            );         
        }
    }

    public function showContainers(Request $request)
    {
        // $perPage = $request->input('per_page',15);
        $sortBy = $request->input('sortBy', 'created_at');  // Lấy giá trị sortBy từ request hoặc dùng mặc định là 'created_at'
        $sortOrder = $request->input('sortOrder', 'asc');   // Cho phép người dùng chỉ định thứ tự sắp xếp asc hoặc desc
        $branch_id = $request->route('branch_id');
        $allProducts = Product::all(); 
        $users = User::all();
        $platforms = Platform::all();
        $query = AuxpackingContainer::where('auxpacking_containers.branch_id', $branch_id)
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->input('status'));
            })
            ->when($request->filled('platform'), function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform'));
            })
            ->when($request->filled('searchProductCode'), function ($q) use ($request) {
                $q->where('product_api_id', $request->input('searchProductCode'));
            })
            ->when($request->filled('searchOrderCode'), function ($q) use ($request) {
                $q->whereHas('order', function ($subQuery) use ($request) {
                    $subQuery->where('order_code', $request->input('searchOrderCode'));  // Tìm kiếm theo mã đơn hàng thông qua mối quan hệ 'order'
                });
            })
            ->when($request->filled('searchContainerCode'), function ($q) use ($request) {
                $q->whereHas('container', function ($subQuery) use ($request) {
                    $subQuery->where('container_code', $request->input('searchContainerCode'));
                });
            })
            ->with(['product', 'productApi', 'order.platform', 'container.location.parent']);
            // ->orderBy('created_at', 'desc');
            // $query->where('auxpacking_containers.branch_id', $branch_id);
            // Join with containers only if sorting by container-related fields
            if ($sortBy === 'container_code' || $sortBy === 'location_id') {
                $query->join('containers', 'auxpacking_containers.container_id', '=', 'containers.id')
                    ->orderBy('containers.' . $sortBy, $sortOrder);
            } else {
                $query->orderBy('auxpacking_containers.' . $sortBy, $sortOrder);
            }
        $allContainers = $query->get();//->paginate($perPage);
        $containers = $allContainers->groupBy('container_id');
        if ($request->ajax()) {
            return response()->json(['containers' => $containers]);
        }
        return view('auxpackings.auxpacking_container', compact('branch_id', 'containers', 'users', 'platforms', 'allProducts'), ['header' => 'Tổng hợp thùng hàng đóng gói']);
    }

    public function showScans(Request $request)
    {
        $perPage = $request->input('per_page',15);
        // Lấy branch_id từ đường dẫn
        $branch_id = $request->route('branch_id');
        $products = ProductApi::all(); 
        $users = User::all();
        $platforms = Platform::all();
        $query = AuxpackingScan::query();
            $query->where('branch_id', $branch_id)//show chi nhánh tương ứng
            ->when($request->filled('user'), function ($q) use ($request) {
                $q->where('user_id', $request->input('user'));
            })
            ->when($request->filled('platform'), function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform'));
            })
            ->when($request->filled('searchTrackingNumber'), function ($q) use ($request) {
                $q->where('tracking_number', $request->input('searchTrackingNumber'));
            })
            ->when($request->filled('searchOrderCode'), function ($q) use ($request) {
                $q->whereHas('order', function ($subQuery) use ($request) {
                    $subQuery->where('order_code', $request->input('searchOrderCode'));  // Tìm kiếm theo mã đơn hàng thông qua mối quan hệ 'order'
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->whereHas('auxpackingOrder', function ($subQuery) use ($request) {
                    $subQuery->where('status', $request->input('status'));
                });
            })
            ->with(['order', 'platform', 'user', 'auxpackingOrder'])
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
            return view('auxpackings.auxpacking_scan', compact(
                'branch_id',
                'orders',
                'users',
                'platforms',
                'initialData'),
                ['header' => 'Quét đơn']
            );         
        }
    }
    
    public function storeScan(Request $request)
    {
        $tracking_number = $request->tracking_number;
        $orderProcess = OrderProcess::where('tracking_number', $tracking_number)->first();
        if (!$orderProcess) {
            return response()->json([
                'message' => 'Không có đơn hàng này',
                'tracking_number' => $tracking_number,
                'status' => 404
            ], 404);
        }
        $order = Order::find($orderProcess->order_id);
        if (!$order) {
            return response()->json([
                'message' => 'Không có thông tin đơn hàng',
                'tracking_number' => $tracking_number,
                'status' => 404
            ], 404);
        }
        // Kiểm tra xem đơn hàng đã được quét chưa
        $existingScan = AuxpackingScan::where('tracking_number', $tracking_number)
                        ->with(['order', 'platform', 'user'])
                        ->first();
        if ($existingScan) {
            return response()->json([
                'message' => 'Mã vận đơn đã tồn tại',
                'data' => $existingScan,
                'tracking_number' => $tracking_number,
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
        $scan->load(['order', 'platform', 'user']);
        // Cập nhật AuxpackingOrder
        $auxpackingOrder = AuxpackingOrder::where('order_id', $scan->order_id)->first();
        if ($auxpackingOrder) {
            $auxpackingOrder->status = 2; // Cập nhật trạng thái
            $auxpackingOrder->save();
        }
        return response()->json([
            'message' => 'Đã lưu!',
            'data' => $scan,
            'tracking_number' => $tracking_number,
            'status' => 200
        ], 200);
    }
    
    public function removeScan(Request $request)
    {
        $data = $request->validate([
            'scanId' => 'required|integer'
        ]);
        $scanId = $data['scanId'];
        try {
            $scan = AuxpackingScan::findOrFail($scanId);
            // Cập nhật AuxpackingOrder
            $auxpackingOrder = AuxpackingOrder::where('order_id', $scan->order_id)->first();
            if ($auxpackingOrder) {
                $auxpackingOrder->status = 1; // Cập nhật lại trạng thái mặc định
                $auxpackingOrder->save();
            }
            $scan->delete();
            return response()->json([
                'message' => 'Xóa thành công',
                // 'auxpackingProduct' => $auxpackingProduct
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Không tìm thấy.'], 404);
        }
    }

    public function updateOrderStatuses(Request $request) {
        $scanIds = $request->selectedOrderIds;
        $status = $request->newStatus;
        $orderIds = AuxpackingScan::whereIn('id', $scanIds)->get()->pluck('order_id');
        AuxpackingOrder::whereIn('order_id', $orderIds)->update(['status' => $status]);
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'orderIds' => $orderIds]);
    }    

}
