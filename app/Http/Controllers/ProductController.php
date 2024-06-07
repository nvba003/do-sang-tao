<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\ProductApi;
use App\Models\Product;
use App\Models\Category;
use App\Models\Bundle;
use App\Models\ProductGroup;
use Carbon\Carbon;
use App\Jobs\FetchAndStoreProductsJob;

class ProductController extends Controller
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
        return back()->with('success', 'Cập nhật sản phẩm thành công!');
        // return response()->json(['message' => 'Products are being processed.']);
    }

    public function showProducts(Request $request)
    {
        $perPage = $request->input('perPage');
        // $containers = Container::all();    
        $categories = Category::all();
        $bundles = Bundle::all();
        $productGroups = ProductGroup::all();
        $query = Product::query()
            ->when($request->filled('searchProductCode'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('searchProductCode') . '%');
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status_id', $request->input('status'));
            })
            ->when($request->filled('paymentStatus'), function ($q) use ($request) {
                $q->where('payment', $request->input('paymentStatus'));
            })
            ->when($request->filled('platform'), function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform'));
            })
            ->when($request->filled('searchCreatedAtFrom'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('searchCreatedAtFrom'));  // Lọc theo ngày tạo từ
            })
            ->when($request->filled('searchCreatedAtTo'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('searchCreatedAtTo'));  // Lọc theo ngày tạo đến
            })
            ->when($request->filled('searchCustomer'), function ($q) use ($request) {
                $q->whereHas('customerAccount', function ($subQuery) use ($request) {
                    $subQuery->where('account_name', 'like', '%' . $request->input('searchCustomer') . '%');  // Tìm kiếm khách hàng theo tên tài khoản
                });
            })
            ->when($request->filled('packingStatus'), function ($q) use ($request) {
                if ($request->input('packingStatus') == '1') {
                    $q->has('auxpackingOrder'); // Có auxpackingOrder
                } else {
                    $q->doesntHave('auxpackingOrder'); // Không có auxpackingOrder
                }
            })
            ->when($request->filled('shipping'), function ($q) use ($request) {
                if ($request->input('shipping') == '1') {
                    $q->whereHas('orderProcess', function ($subQuery) {
                        $subQuery->whereNotNull('tracking_number');
                    });
                } else {
                    $q->whereDoesntHave('orderProcess', function ($subQuery) {
                        $subQuery->whereNotNull('tracking_number');
                    });
                }
            })
            ->with(['category', 'bundles.bundleItems', 'productGroup'])
            ->orderBy('created_at', 'desc');
        $products = $query->paginate($perPage);
        if ($request->ajax()) {
            return response()->json([
                'products' => $products->items(),
                'links' => $products->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
        } else {
            // For initial page load, send JSON as part of the rendered page
            $initialData = json_encode([
                'products' => $products->items(),
                'links' => $products->links('vendor.pagination.custom-tailwind')->toHtml(),
            ]);
            return view('products.product', compact(
                'categories',
                'bundles',
                'productGroups',
                'initialData'),
                ['header' => 'Xử lý đơn hàng']
            );         
        }
    }


}
