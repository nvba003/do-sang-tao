<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\ProductApi;
use App\Models\Product;
//use Illuminate\Support\Carbon;

class ProductApiController extends Controller
{
    public function fetchAndStoreProducts()
    {
        $client = new Client();

        $response = $client->request('GET', 'https://do-vat-sang-tao.mysapo.net/admin/products.json', [
            'auth' => ['4ccfe3d9305b4288bb2b5cf9184c8e5d', 'c9830e0a36b348c786f8df30a72d75c8']
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $products = collect($data['products'])->map(function ($item) {
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
                // 'created_at' => Carbon::now()->toDateTimeString(),
                // 'updated_at' => Carbon::now()->toDateTimeString()
            ];
        });

        // Lưu từng sản phẩm vào cơ sở dữ liệu
        foreach ($products as $product) {
            ProductApi::updateOrCreate(['id' => $product['id']], $product);
            $this->handleProductApi($product);//gọi hàm cùng controller
        }
    
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
                //'name' => $productApi->name, // Ví dụ
                // Thêm các trường khác ở đây
            ]
        );
    }

}
