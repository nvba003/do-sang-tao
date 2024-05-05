<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\ProductApi;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Container;
use App\Models\ProductContainer;
use App\Models\ContainerMenuOption;
use App\Models\ContainerStatus;
use App\Models\Location;
//use Illuminate\Support\Facades\DB;

class ContainerController extends Controller
{
    public function showContainers(Request $request)
    {
        $perPage = $request->input('per_page',20);
        // Kiểm tra quyền
        //if (!Gate::allows('add-container')) {
            //abort(403);
        //}
        // if (Gate::denies('add-container')) {
        //     abort(403);
        // }

        $products = ProductApi::all();
        $branches = Branch::all();
        $containerMenuOptions = ContainerMenuOption::all();
        $existingCodes = Container::pluck('id')->toArray();
        $containerStatuses = ContainerStatus::all();
        $locations = Location::all();
        $query = Container::query()
            ->with(['transaction', 'inventoryTransaction', 'productapi'])
            ->when($request->filled('container_id'), function ($q) use ($request) {
                $q->where('container_id', $request->input('container_id'));
            })
            ->orderBy('id', 'desc');

        $containers = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('containers.partial_container_table', compact('containers'))->render();
            $links = $containers->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        
        $header = 'Quản lý thùng hàng';
        return view('containers.container', compact('products', 'branches', 'containerMenuOptions', 'containers', 'existingCodes', 'containerStatuses', 'locations', 'header'));
        //return view('containers.show', compact('products', 'branches', 'containerMenuOptions', 'existingCodes'), ['header' => 'Quản lý thùng hàng']);
    }

    public function getContainers()
    {
        $containers = Container::with(['location.parent', 'productapi', 'branch'])->paginate(4);// Sử dụng khi chọn phân trang
        return response()->json(['containers' => $containers]);
    }

    public function searchProduct(Request $request)
    {
        $productId = $request->input('search_product_id');
        $containers = Container::with(['location.parent', 'productapi', 'branch'])->where('product_id', $productId)->paginate(2); // Phân trang cho kết quả
        // Trả về kết quả dưới dạng JSON
        return response()->json(['search_product_containers' => $containers]);

        // Thực hiện truy vấn tìm kiếm dựa trên tên sản phẩm
        //$products = Product::where('name', 'like', '%' . $searchTerm . '%')->get();
        // Trả về kết quả dưới dạng JSON
        //return response()->json($searchTerm);
    }

    public function searchContainer(Request $request)
    {
        $containerId = $request->container_id;
        $containers = Container::with(['location.parent', 'productapi', 'branch'])->where('id', $containerId)->paginate(2); // Phân trang cho kết quả
        return response()->json(['search_containers' => $containers]);
    }

    public function store(Request $request)
    {
        //dd($request);
        // Validate dữ liệu request
        $validatedData = $request->validate([
            'id' => 'required|string|max:7|unique:containers,container_id', // Sửa tên trường thành 'id'
            'productId' => 'required|exists:products,product_api_id', // Đảm bảo giá trị này tồn tại trong bảng `products`
            'unit' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
        ]);
        
        // Tạo container mới và lưu vào database sử dụng Mass Assignment
        $container = Container::create([
            'container_id' => $validatedData['id'], // Sửa thành 'id' để khớp với tên trường được validate
            'product_id' => $validatedData['productId'],
            'unit' => $validatedData['unit'],
            'branch_id' => $validatedData['branch_id'],
        ]);
        // Lưu quan hệ giữa container và sản phẩm vào bảng ProductContainers
        //$productContainer = new ProductContainer();
        //$productContainer->container_id = $validatedData['id'];
        //$productContainer->save();

        // Kiểm tra nếu cần tạo container cho chi nhánh khác (sử dụng branch_box)
        if ($request->has('branch_box')) {
            $containerId = $validatedData['id'];
            $branchId = substr($containerId, 0, 1); // Lấy mã chi nhánh từ mã thùng
            $otherBranchId = $branchId == '1' ? '2' : '1'; // Đổi mã chi nhánh
            $otherContainerId = $otherBranchId . substr($containerId, 1); // Tạo mã thùng mới cho chi nhánh khác
            // Lưu thùng hàng cho chi nhánh khác
            Container::create([
                'container_id' => $otherContainerId,
                'product_id' => $validatedData['productId'],
                'unit' => $validatedData['unit'],
                'branch_id' => $validatedData['branch_id'],
            ]);
        }

        // Quay lại trang trước đó với thông báo thành công
        return back()->with('success', 'Thùng hàng đã được tạo thành công!');
    }
}
