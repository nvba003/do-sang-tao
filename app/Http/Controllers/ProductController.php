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
use App\Models\BundleItem;
use App\Models\ProductGroup;
use Carbon\Carbon;
use App\Jobs\FetchAndStoreProductsJob;
use Illuminate\Support\Facades\Auth;

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
        $allProducts = Product::all(); 
        $categories = Category::all();
        $bundles = Bundle::all();
        $productGroups = ProductGroup::all();
        $query = Product::query()
            ->when($request->filled('searchProductCode'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('searchProductCode') . '%')
                ->orWhere('sku', 'like', '%' . $request->input('searchProductCode') . '%');
            })
            ->when($request->filled('group'), function ($q) use ($request) {
                $q->where('product_group_id', $request->input('group'));
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->input('status');
                $q->where(function ($subQuery) {
                    $subQuery->where('sku', 'like', 'G4%')
                             ->orWhere('sku', 'like', 'COMBO%');
                });
                if ($status == 0) {
                    $q->whereNull('bundle_id');
                } elseif ($status == 1) {
                    $q->whereNotNull('bundle_id');
                }
            })
            ->with(['category', 'bundle.bundleItems.product', 'productGroup'])
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
                'allProducts',
                'initialData'),
                ['header' => 'Xử lý đơn hàng']
            );         
        }
    }
    
    public function updateInfoProduct(Request $request)
    {
        $product = Product::find($request->id);
        $product->sku = $request->sku;
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->product_group_id = $request->product_group_id;
        $product->save();
        return response()->json(['message' => 'Đã cập nhật sản phẩm']);
    }

    public function updateSizeWeight(Request $request)
    {
        $product = Product::find($request->id);
        $product->length = $request->length;
        $product->height = $request->height;
        $product->width = $request->width;
        $product->weight = $request->weight;
        $product->save();
        return response()->json(['message' => 'Đã cập nhật sản phẩm']);
    }
    
    public function saveBundleItems(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:bundles,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'bundle_items' => 'required|array',
        ]);

        if (empty($request->id)) {
            $bundle = Bundle::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'type' => $request->type,
                'description' => $validated['description']
            ]);
        } else {
            $bundle = Bundle::findOrFail($request->id);
            $bundle->update([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'type' => $request->type,
                'description' => $validated['description']
            ]);
        }
        $product = Product::find($request->product_id);
        $product->bundle_id = $bundle->id;
        $product->save();

        if (!empty($validated['bundle_items'])) {
            $bundleItemsData = $request->bundle_items;
            foreach ($bundleItemsData as $itemData) {
                BundleItem::updateOrCreate(
                    ['bundle_id' => $bundle->id, 'product_api_id' => $itemData['product_api_id']],
                    [
                        'quantity' => $itemData['quantity']
                    ]
                );
            }
            // Xóa những bundle items không có trong request
            $existingProductApiIds = array_column($bundleItemsData, 'product_api_id');// lấy danh sách tất cả product_api_id có trong request
            $bundle->bundleItems()
                ->where('bundle_id', $bundle->id)
                ->whereNotIn('product_api_id', $existingProductApiIds)
                ->delete();
        }
        return response()->json(['message' => 'Bundle saved successfully!', 'bundle_id' => $bundle->id]);
    }

    public function destroyBundle($bundleId)
    {
        $bundle = Bundle::find($bundleId);
        if (!$bundle) {
            return response()->json(['message' => 'Bundle not found'], 404);
        }
        // Kiểm tra quyền người dùng, chỉ cho phép admin hoặc manager xóa bundle
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('manager')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        Product::where('bundle_id', $bundleId)->update(['bundle_id' => null]);
        $bundle->delete();
        return response()->json(['message' => 'Bundle deleted successfully']);
    }


}
