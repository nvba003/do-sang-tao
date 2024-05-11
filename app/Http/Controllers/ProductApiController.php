<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Models\ProductApi;
use App\Models\Product;
//use Illuminate\Support\Carbon;

class ProductApiController extends Controller
{
    // public function fetchAndStoreProducts()
    // {
    //     $client = new Client();

    //     $response = $client->request('GET', 'https://do-vat-sang-tao.mysapo.net/admin/products.json', [
    //         'auth' => ['4ccfe3d9305b4288bb2b5cf9184c8e5d', 'c9830e0a36b348c786f8df30a72d75c8']
    //     ]);
    //     $data = json_decode($response->getBody()->getContents(), true);
    //     dd($data);
    //     $products = collect($data['products'])->map(function ($item) {
    //         return [
    //             'id' => $item['variants'][0]['id'],
    //             'sku' => $item['variants'][0]['sku'],
    //             'name' => $item['name'],
    //             'product_type' => $item['product_type'],
    //             'images' => $item['images'][0]['src'] ?? null,
    //             'alias' => $item['alias'],
    //             'inventory_quantity' => $item['variants'][0]['inventory_quantity'],
    //             'price' => $item['variants'][0]['price'],
    //             'weight' => $item['variants'][0]['weight'],
    //             'created_at' => $item['variants'][0]['created_on'],
    //             'updated_at' => $item['variants'][0]['modified_on']
    //             // 'created_at' => Carbon::now()->toDateTimeString(),
    //             // 'updated_at' => Carbon::now()->toDateTimeString()
    //         ];
    //     });

    //     // Lưu từng sản phẩm vào cơ sở dữ liệu
    //     foreach ($products as $product) {
    //         ProductApi::updateOrCreate(['id' => $product['id']], $product);
    //         $this->handleProductApi($product);//gọi hàm cùng controller
    //     }
    
    //     return back()->with('success', 'Cập nhật sản phẩm thành công!');
    // }

    public function fetchAndStoreProducts()
{
    $allProducts = [];
    $page = 1;
    $perPage = 50; // Số sản phẩm mỗi trang, có thể thay đổi nếu cần thiết
    $totalPages = 1; // Giá trị mặc định, sẽ cập nhật sau khi lấy dữ liệu
    $totalProducts = 0; // Tổng số sản phẩm

    do {
        // Tạo URL với phân trang
        $url = "https://do-vat-sang-tao.mysapo.net/admin/products.json?page=$page&limit=$perPage";

        // Khởi tạo cURL
        $ch = curl_init();

        // Thiết lập các tùy chọn cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "4ccfe3d9305b4288bb2b5cf9184c8e5d:c9830e0a36b348c786f8df30a72d75c8");

        // Thực hiện yêu cầu cURL và nhận dữ liệu phản hồi
        $response = curl_exec($ch);
        curl_close($ch);

        // Giải mã JSON từ phản hồi
        $data = json_decode($response, true);

        if (isset($data['products']) && !empty($data['products'])) {
            $allProducts = array_merge($allProducts, $data['products']);
        }

        // Kiểm tra nếu phản hồi chứa tổng số sản phẩm
        if (isset($data['total'])) {
            $totalProducts = $data['total'];
            $totalPages = ceil($totalProducts / $perPage);
        }

        $page++;
    } while ($page <= $totalPages); // Tiếp tục cho đến khi đạt đến tổng số trang

    $products = collect($allProducts)->map(function ($item) {
        return [
            'id' => $item['variants'][0]['id'],
            'sku' => $item['variants'][0]['sku'],
            'name' => $item['name'],
            'product_type' => $item['product_type'],
            'images' => $item['images'][0]['src'] ?? null,
            'alias' => $item['alias'],
            'inventory_quantity' => $item['variants'][0]['inventory_quantity'],
            'price' => $item['variants'][0]['price'],
            'weight' => $item['variants'][0]['weight'],
            'created_at' => $item['variants'][0]['created_on'],
            'updated_at' => $item['variants'][0]['modified_on']
        ];
    });

    // Lưu từng sản phẩm vào cơ sở dữ liệu
    DB::transaction(function () use ($products) {
        foreach ($products as $product) {
            ProductApi::updateOrCreate(['id' => $product['id']], $product);
            $this->handleProductApi($product); // Gọi hàm cùng controller
        }
    });

    return back()->with('success', 'Cập nhật sản phẩm thành công!');
}

public function handleProductApi($productApiData)
{
    $productApi = ProductApi::updateOrCreate(
        ['id' => $productApiData['id']],
        $productApiData
    );

    // Kiểm tra và thêm mới Product nếu không tìm thấy
    $product = Product::firstOrCreate(
        ['product_api_id' => $productApi->id],
        [
            // Thêm các trường khác ở đây nếu cần
        ]
    );
}


    // public function handleProductApi($productApiData)
    // {
    //     $productApi = ProductApi::updateOrCreate(
    //         ['id' => $productApiData['id']],
    //         $productApiData
    //     );

    //     // Kiểm tra và thêm mới Product nếu không tìm thấy
    //     $product = Product::firstOrCreate(
    //         ['product_api_id' => $productApi->id],
    //         [
    //             //'name' => $productApi->name, // Ví dụ
    //             // Thêm các trường khác ở đây
    //         ]
    //     );
    // }

    public function showWebProducts(Request $request)
    {
        $perPage = $request->input('per_page',20); // Số lượng mặc định là 3 nếu không có tham số per_page
        $query = ProductApi::query()
            ->with(['containers'])
            ->when($request->filled('created_at'), function ($q) use ($request) {
                $q->whereDate('created_at', $request->created_at);
            })
            ->orderBy('name', 'desc'); // Sắp xếp theo report_date giảm dần

        $products = $query->paginate($perPage);

        if ($request->ajax()) {
            $view = view('products.partial_show_web_products', compact('products'))->render();
            $links = $products->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);//, 'products' => $products
        }

        $header = 'Danh sách sản phẩm';
        return view('products.show_web_products', compact('products', 'header'));
    }

}
