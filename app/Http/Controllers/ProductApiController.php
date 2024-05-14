<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\ProductApi;
use App\Models\Product;
use Carbon\Carbon;
use App\Jobs\FetchAndStoreProductsJob;
//use Illuminate\Support\Carbon;

class ProductApiController extends Controller
{
    // public function getDataByPage($page)
    // {
    //     $spikey = "4ccfe3d9305b4288bb2b5cf9184c8e5d";
    //     $apisecret = "c9830e0a36b348c786f8df30a72d75c8";
    //     $GetProducts = "@do-vat-sang-tao.mysapo.net/admin/products";
    //     $Fields = ".json?fields=image,name,variants,product_type,alias";
    //     $Indexs = "&limit=250&page=";//giới hạn 250 bản ghi
    //     //$Page = "2";
    //     //$Count = "/count.json";
    //     $LinkGetdataSapo = 'https://'.$spikey.':'.$apisecret.$GetProducts.$Fields.$Indexs.$page;
    //     $response = Http::get($LinkGetdataSapo);
    //     $data = json_decode($response); // '{"id": 1420053, "name": "guzzle", ...}'
    //     return $data;
    // }
    // public function fetchAndStoreProducts()
    // {
        
    //     $countProduct = 'https://4ccfe3d9305b4288bb2b5cf9184c8e5d:c9830e0a36b348c786f8df30a72d75c8@do-vat-sang-tao.mysapo.net/admin/products/count.json';
    //     $response = Http::get($countProduct);
    //     $count = json_decode($response)->count;//lấy tổng số sản phẩm chính
    //     $page = ceil($count/250);//tính số trang
    //     $collectedData = collect();
    //     for ($i = 1; $i <= $page; $i++) {//chia nhỏ để ghi dữ liệu với 250 bản ghi/lần có thời gian tối ưu nhất
    //         $kq = collect();//tạo mảng php trống
    //         $dulieu = $this->getDataByPage($i)->products;
    //         foreach ($dulieu as $value) {
    //             if(empty($value->image)){
    //                 $image = '';
    //             }
    //             else{
    //                 $image = $value->image->src;
    //             }
    //             foreach ($value->variants as $giatri) {
    //                 $kq->push([
    //                     'id'        => $giatri->id,
    //                     'sku'       => $giatri->sku,
    //                     'name'=> $giatri->title == "Default Title" ? $value->name : $value->name .' '. $giatri->title,
    //                     'product_type'      => $value->product_type,
    //                     'images'   => $image ?? null,
    //                     'alias'     => $value->alias,
    //                     'inventory_quantity'   => $giatri->inventory_quantity,
    //                     'price'    => $giatri->price,
    //                     'weight'   => $giatri->weight,
    //                     'updated_at'=> Carbon::now('Asia/Ho_Chi_Minh')
    //                 ]);

    //             }//end foreach $value
    //         }//end foreach $dulieu
    //         $collectedData = $collectedData->concat($kq);//tổng hợp dữ liệu từng trang

    //         //-------cập nhật dữ liệu bảng sapoweb--------
    //         DB::table('product_apis')->upsert($kq->all(), //hoặc $kq->toArray() đều được
    //             ['id'], ['sku','name','product_type','images','alias','inventory_quantity','price','weight','updated_at']);

    //     }//end for

    //     return $collectedData;
    // }

    public function fetchAndStoreProducts()
    {
        $countProduct = 'https://4ccfe3d9305b4288bb2b5cf9184c8e5d:c9830e0a36b348c786f8df30a72d75c8@do-vat-sang-tao.mysapo.net/admin/products/count.json';
        $response = Http::get($countProduct);
        $count = json_decode($response->body())->count;
        $pages = ceil($count / 250);
        //dd($pages);
        for ($i = 1; $i <= $pages; $i++) {
            FetchAndStoreProductsJob::dispatch($i);
        }
        //return back()->with('success', 'Cập nhật sản phẩm thành công!');
        return response()->json(['message' => 'Products are being processed.']);
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
