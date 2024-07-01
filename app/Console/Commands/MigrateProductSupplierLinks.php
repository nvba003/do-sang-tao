<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateProductSupplierLinks extends Command
{
    protected $signature = 'migrate:productsupplierlinks';
    protected $description = 'Migrate product supplier links from banggianhaps to product_supplier_links';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting data migration...');

        $this->migrateData();

        $this->info('Data migration completed successfully.');
    }

    protected function migrateData()
    {
        function extractSupplierData($supplierField) {
            $parts = explode("\n", $supplierField);
            return [
                'name' => $parts[0] ?? null,
                'link' => $parts[1] ?? null
            ];
        }

        function getSupplierInfo($supplierName) {
            return DB::table('suppliers')->where('name', $supplierName)->first(['id', 'supplier_group_id']);
        }

        function insertProductSupplierLink($data) {
            DB::table('product_supplier_links')->insert($data);
        }

        function linkExists($productApiId, $supplierId) {
            return DB::table('product_supplier_links')
                ->where('product_api_id', $productApiId)
                ->where('supplier_id', $supplierId)
                ->exists();
        }

        $products = DB::table('banggianhaps')->get();

        foreach ($products as $product) {
            $productApiId = $product->id;  // Lấy id từ banggianhaps làm product_api_id

            // Kiểm tra xem product_api_id có tồn tại trong bảng products hay không
            if (DB::table('products')->where('product_api_id', $productApiId)->exists()) {
                for ($i = 1; $i <= 5; $i++) {
                    $supplierField = 'ncc' . $i;
                    if (!empty($product->$supplierField)) {
                        $supplierData = extractSupplierData($product->$supplierField);
                        if (!empty($supplierData['name']) && !empty($supplierData['link'])) {
                            $supplierInfo = getSupplierInfo($supplierData['name']);
                            if ($supplierInfo) {
                                if (!linkExists($productApiId, $supplierInfo->id)) {
                                    insertProductSupplierLink([
                                        'product_api_id' => $productApiId,
                                        'supplier_id' => $supplierInfo->id,
                                        'supplier_product_id' => null, // Bạn cần cung cấp giá trị này nếu có
                                        'supplier_product_sku_id' => null, // Bạn cần cung cấp giá trị này nếu có
                                        'supplier_group_id' => $supplierInfo->supplier_group_id,
                                        'url' => $supplierData['link'],
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);
                                }
                            }
                        }
                    }
                }
            } else {
                $this->warn("Product API ID {$productApiId} does not exist in products table.");
            }
        }
    }
}
