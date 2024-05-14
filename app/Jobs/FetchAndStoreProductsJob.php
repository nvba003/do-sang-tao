<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;//thêm vào
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FetchAndStoreProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $page;

    public function __construct($page)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $spikey = "4ccfe3d9305b4288bb2b5cf9184c8e5d";
        $apisecret = "c9830e0a36b348c786f8df30a72d75c8";
        $GetProducts = "@do-vat-sang-tao.mysapo.net/admin/products";
        $Fields = ".json?fields=image,name,variants,product_type,alias";
        $Indexs = "&limit=250&page=";
        $LinkGetdataSapo = 'https://'.$spikey.':'.$apisecret.$GetProducts.$Fields.$Indexs.$this->page;
        $response = Http::get($LinkGetdataSapo);
        $data = json_decode($response->body())->products;

        $products = collect($data)->map(function ($item) {
            $image = $item->image ? $item->image->src : null;
            return collect($item->variants)->map(function ($variant) use ($item, $image) {
                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'name' => $variant->title == "Default Title" ? $item->name : $item->name .' '. $variant->title,
                    'product_type' => $item->product_type,
                    'images' => $image,
                    'alias' => $item->alias,
                    'inventory_quantity' => $variant->inventory_quantity,
                    'price' => $variant->price,
                    'weight' => $variant->weight,
                    'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')
                ];
            });
        })->collapse();
        //dd($products);
        DB::table('product_apis')->upsert($products->toArray(), ['id'], ['sku','name','product_type','images','alias','inventory_quantity','price','weight','updated_at']);
        // Lấy danh sách tất cả các product_api_id từ bảng product_apis
        $apiIds = DB::table('product_apis')->pluck('id');
        // Lấy danh sách các product_api_id đã tồn tại trong bảng products
        $existingApiIdsInProducts = DB::table('products')->whereIn('product_api_id', $apiIds)->pluck('product_api_id');
        // Tìm các product_api_id mới không có trong bảng products
        $newApiIds = $apiIds->diff($existingApiIdsInProducts);
        // Giả sử bạn muốn thêm các product_api_id mới này vào các sản phẩm cụ thể hoặc tạo sản phẩm mới
        foreach ($newApiIds as $newApiId) {
            // Thêm product_api_id mới vào bảng products
            // Bạn cần thay đổi logic này phù hợp với nhu cầu cụ thể của bạn (ví dụ: tạo sản phẩm mới hoặc cập nhật sản phẩm hiện có)
            DB::table('products')->insert([
                'product_api_id' => $newApiId,
                // Thay đổi các trường dữ liệu cần thiết
                'created_at' => Carbon::now(),
                // 'updated_at' => Carbon::now()
            ]);
        }

    }

}
