<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RapidAPIService;
use App\Models\SupplierProduct;
use App\Models\SupplierProductImage;
use App\Models\SupplierProductProperty;
use App\Models\SupplierProductSku;

class RapidController extends Controller
{
    protected $rapidAPIService;

    public function __construct(RapidAPIService $rapidAPIService)
    {
        $this->rapidAPIService = $rapidAPIService;
    }

    public function fetchData(Request $request)
    {
        $endpoint = 'https://taobao-tmall-16881.p.rapidapi.com/api/tkl/v2/item/detail';
        $params = [
            'provider' => '1688',
            'id' => '575608280628',
        ];

        $data = $this->rapidAPIService->getData($endpoint, $params);
        
        $item = $data['item'];
        // dd($item);

        // Lưu dữ liệu vào bảng SupplierProduct
        $supplierProduct = SupplierProduct::create([
            'supplier_id' => 1, // Thay đổi theo nhu cầu của bạn
            'product_api_id' => null, // Thay đổi theo nhu cầu của bạn
            'supplier_product_id' => $item['num_iid'],
            'supplier_product_url' => $item['detail_url'],
            'num_iid' => $item['num_iid'],
            'title' => $item['title'],
            'desc_short' => $item['desc_short'] ?? null,
            'price' => $item['price'] ?? null,
            'orginal_price' => $item['orginal_price'] ?? null,
            'nick' => $item['nick'] ?? null,
            'num' => $item['num'] ?? null,
            'detail_url' => $item['detail_url'] ?? null,
            'pic_url' => $item['pic_url'] ?? null,
            'desc' => $item['desc'] ?? null,
            'min_order' => $item['min_num'] ?? null,
            'available' => true,
        ]);

        // Lưu hình ảnh vào bảng SupplierProductImage
        if (!empty($item['desc_img'])) {
            foreach ($item['desc_img'] as $img) {
                SupplierProductImage::create([
                    'supplier_product_id' => $supplierProduct->id,
                    'url' => $img,
                ]);
            }
        }

        // Lưu thuộc tính sản phẩm vào bảng SupplierProductProperty
        if (!empty($item['item_prop_list'])) {
            foreach ($item['item_prop_list'] as $prop) {
                foreach ($prop['prop_value'] as $value) {
                    SupplierProductProperty::create([
                        'supplier_product_id' => $supplierProduct->id,
                        'property_id' => $prop['id'] ?? null,
                        'name' => $prop['name'] ?? null,
                        'value' => $value['value'] ?? null,
                    ]);
                }
            }
        }

        // Lưu SKU vào bảng SupplierProductSku
        if (!empty($item['item_sku_list'])) {
            foreach ($item['item_sku_list'] as $sku) {
                SupplierProductSku::create([
                    'supplier_product_id' => $supplierProduct->id,
                    'sku_id' => $sku['item_id'],
                    'price' => $sku['price_real'] ?? null,
                    'prop_id' => $sku['prop_id'] ?? null,
                    'prop_value' => $sku['prop_value'] ?? null,
                    'storage' => $sku['storage'] ?? null,
                ]);
            }
        }

        return response()->json($supplierProduct);
    }
}
