<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RapidAPIService;
use App\Models\SupplierProduct;
use App\Models\SupplierProductImage;
use App\Models\SupplierProductProperty;
use App\Models\SupplierProductSku;
use App\Models\ProductSupplierLink;

class RapidController extends Controller
{
    protected $rapidAPIService;

    public function __construct(RapidAPIService $rapidAPIService)
    {
        $this->rapidAPIService = $rapidAPIService;
    }

    public function fetchData(Request $request)
    {
        // Lấy dữ liệu từ request
        $supplierId = $request->input('supplier_id');
        $provider = $request->input('provider');
        $productId = $request->input('product_id');
        $productApiId = $request->input('product_api_id');

        // Xác định endpoint và params dựa trên provider
        $endpoint = 'https://taobao-tmall-16881.p.rapidapi.com/api/tkl/v2/item/detail';
        $params = [
            'provider' => $provider,
            'id' => $productId,
        ];

        try {
            // Gọi API để lấy dữ liệu sản phẩm
            $data = $this->rapidAPIService->getData($endpoint, $params);
            // Kiểm tra phản hồi từ API
            if (!isset($data['item'])) {
                return response()->json(['error' => 'Phản hồi từ API không chứa thông tin sản phẩm'], 500);
            }
            $item = $data['item'];

            // Lưu dữ liệu vào bảng SupplierProduct
            $supplierProduct = SupplierProduct::updateOrCreate(
                ['num_iid' => $item['num_iid']], // Điều kiện tìm kiếm
                [
                    'supplier_id' => $supplierId,
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
                ]
            );

            // Lưu hoặc cập nhật hình ảnh vào bảng SupplierProductImage
            $existingImageIds = [];
            if (!empty($item['desc_img'])) {
                foreach ($item['desc_img'] as $index => $img) {
                    $image = SupplierProductImage::updateOrCreate(
                        [
                            'supplier_product_id' => $supplierProduct->id,
                            'sort_order' => $index
                        ],
                        [
                            'url' => $img,
                            'sort_order' => $index
                        ]
                    );
                    $existingImageIds[] = $image->id;
                }
            }
            SupplierProductImage::where('supplier_product_id', $supplierProduct->id)
                ->whereNotIn('id', $existingImageIds)
                ->delete();
            // Lưu hoặc cập nhật thuộc tính sản phẩm vào bảng SupplierProductProperty
            $existingPropertyIds = [];
            if (!empty($item['item_prop_list'])) {
                foreach ($item['item_prop_list'] as $prop) {
                    foreach ($prop['prop_value'] as $index => $value) {
                        $property = SupplierProductProperty::updateOrCreate(
                            [
                                'supplier_product_id' => $supplierProduct->id,
                                'sort_order' => $index
                            ],
                            [
                                'property_id' => $prop['id'] ?? null,
                                'name' => $prop['name'] ?? null,
                                'value' => $value['value'] ?? null,
                                'sort_order' => $index
                            ]
                        );
                        $existingPropertyIds[] = $property->id;
                    }
                }
            }
            SupplierProductProperty::where('supplier_product_id', $supplierProduct->id)
                ->whereNotIn('id', $existingPropertyIds)
                ->delete();
            // Lưu hoặc cập nhật SKU vào bảng SupplierProductSku
            $existingSkuIds = [];
            $singleSkuId = null;
            if (!empty($item['item_sku_list'])) {
                foreach ($item['item_sku_list'] as $index => $sku) {
                    $skuEntry = SupplierProductSku::updateOrCreate(
                        [
                            'supplier_product_id' => $supplierProduct->id,
                            'sku_id' => $sku['item_id'],
                        ],
                        [
                            'price' => $sku['price'] ?? null,
                            'price_real' => $sku['price_real'] ?? null,
                            'prop_id' => $sku['prop_id'] ?? null,
                            'prop_value' => $sku['prop_value'] ?? null,
                            'storage' => $sku['storage'] ?? null,
                            'sort_order' => $index
                        ]
                    );
                    $existingSkuIds[] = $skuEntry->id;
                    // Lấy ID của SKU duy nhất nếu chỉ có một phần tử
                    if (count($item['item_sku_list']) == 1) {
                        $singleSkuId = $skuEntry->id;
                    }
                }
            }
            SupplierProductSku::where('supplier_product_id', $supplierProduct->id)
                ->whereNotIn('id', $existingSkuIds)
                ->delete();

            // Cập nhật hoặc tạo mới product_supplier_links
            ProductSupplierLink::updateOrCreate(
                [
                    'product_api_id' => $productApiId,
                    'supplier_id' => $supplierId
                ],
                [
                    'supplier_product_id' => $supplierProduct->id,
                    'supplier_product_sku_id' => $singleSkuId,
                    // 'url' => $item['detail_url'] ?? null,
                    'updated_at' => now()
                ]
            );

            // Trả về dữ liệu SupplierProduct cùng với các quan hệ
            $supplierProduct = SupplierProduct::with('supplier.group')->find($supplierProduct->id);
            return response()->json($supplierProduct);
        } catch (\Exception $e) {
            // Xử lý lỗi và phản hồi cho client
            return response()->json(['error' => 'Đã có lỗi xảy ra trong quá trình xử lý dữ liệu', 'message' => $e->getMessage()], 500);
        }
    }

}
