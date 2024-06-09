<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\ProductApi;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Container;
use App\Models\ProductContainer;
use App\Models\Category;
use App\Models\ContainerMenuOption;
use App\Models\ContainerStatus;
use App\Models\Location;
use App\Models\BundleType;
//use Illuminate\Support\Facades\DB;

class ContainerController extends Controller
{
    public function showContainers(Request $request)
    {
        $perPage = $request->input('per_page',10);
        // Kiểm tra quyền
        //if (!Gate::allows('add-container')) {
            //abort(403);
        //}
        // if (Gate::denies('add-container')) {
        //     abort(403);
        // }

        $products = ProductApi::all();
        $branches = Branch::all();
        // $bundleTypes = BundleType::all();
        // $containerMenuOptions = ContainerMenuOption::all();//cho chức năng thêm thùng
        $categories = Category::all();//cho chức năng thêm thùng
        $existingCodes = Container::pluck('container_code')->toArray();
        $containerStatuses = ContainerStatus::all();
        $locations = Location::all();
        $query = Container::query()
            ->with(['transaction', 'inventoryTransaction', 'productapi'])
            ->when($request->filled('searchProductID'), function ($q) use ($request) {
                $q->where('product_id', $request->input('searchProductID'));
            })
            ->when($request->filled('parent_location_id'), function ($query) use ($request) {
                $query->whereHas('location', function ($q) use ($request) {
                    $q->where('parent_id', $request->input('parent_location_id'));
                });
            })
            ->when($request->filled('branch_id'), function ($q) use ($request) {
                $q->whereHas('branch', function ($q) use ($request) {
                    $q->where('id', $request->input('branch_id'));
                });
            })
            ->when($request->filled('container_status_id'), function ($q) use ($request) {
                $q->where('container_status_id', $request->input('container_status_id'));
            })
            // ->when($request->filled('bundle_type'), function ($query) use ($request) {
            //     $query->whereHas('product', function ($q) use ($request) {
            //         $q->where('bundle_type_id', $request->input('bundle_type'));
            //     });
            // }) 
            ->when($request->filled('container_id'), function ($q) use ($request) {
                $q->where('container_code', $request->input('container_id'));
            })
            ->when($request->filled('location_id'), function ($q) use ($request) {
                $q->where('location_id', $request->input('location_id'));
            })
            ->orderBy('id', 'desc');

        $containers = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('containers.partial_container_table', compact('containers'))->render();
            $links = $containers->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        
        $header = 'Quản lý thùng hàng';
        return view('containers.container', compact('products', 'branches', 'categories', 'containers', 'existingCodes', 'containerStatuses', 'locations', 'header'));
    }

    // public function searchProduct(Request $request)
    // {
    //     $productId = $request->input('search_product_id');
    //     $containers = Container::with(['location.parent', 'productapi', 'branch'])->where('product_id', $productId)->paginate(2); // Phân trang cho kết quả
    //     // Trả về kết quả dưới dạng JSON
    //     return response()->json(['search_product_containers' => $containers]);

    //     // Thực hiện truy vấn tìm kiếm dựa trên tên sản phẩm
    //     //$products = Product::where('name', 'like', '%' . $searchTerm . '%')->get();
    //     // Trả về kết quả dưới dạng JSON
    //     //return response()->json($searchTerm);
    // }

    // public function searchContainer(Request $request)
    // {
    //     $containerId = $request->container_id;
    //     $containers = Container::with(['location.parent', 'productapi', 'branch'])->where('id', $containerId)->paginate(2); // Phân trang cho kết quả
    //     return response()->json(['search_containers' => $containers]);
    // }

    public function store(Request $request)
    {
        // dd($request);
        // Validate dữ liệu request
        $validatedData = $request->validate([
            'id' => 'required|string|max:7|unique:containers,container_code', // Sửa tên trường thành 'id'
            'productId' => 'required|exists:products,product_api_id', // Đảm bảo giá trị này tồn tại trong bảng `products`
            'unit' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
        ]);
        
        // Tạo container mới và lưu vào database sử dụng Mass Assignment
        $container = Container::create([
            'container_code' => $validatedData['id'], // Sửa thành 'id' để khớp với tên trường được validate
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
                'container_code' => $otherContainerId,
                'product_id' => $validatedData['productId'],
                'unit' => $validatedData['unit'],
                'branch_id' => $otherBranchId,
            ]);
        }

        // Quay lại trang trước đó với thông báo thành công
        return back()->with('success', 'Thùng hàng đã được tạo thành công!');
    }

    public function indexLocation()
    {
        $locations = Location::with('children')->whereNull('parent_id')->get();
        return view('containers.location', compact('locations'), ['header' => 'Vị trí thùng hàng']);
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'location_name' => 'required',
        ]);
        $data = $request->all();
        if ($request->filled('location_id')) {
            // Update existing location
            $location = Location::find($request->location_id);
            if ($location) {
                $location->update($data);
            }
        } else {
            // Create new location
            Location::create($data);
        }
        return redirect()->route('locations.index');
    }

    public function destroyLocation($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();
        return redirect()->route('locations.index');
    }
}
