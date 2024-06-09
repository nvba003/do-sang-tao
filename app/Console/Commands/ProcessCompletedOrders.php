<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Auxpacking\AuxpackingOrder;
use App\Models\Auxpacking\AuxpackingProduct;
use App\Models\Auxpacking\AuxpackingContainer;
use App\Models\Auxpacking\AuxpackingScan;
use App\Models\Product;
use App\Models\Container;
use App\Models\ProductTransaction;
use App\Models\InventoryTransaction;
use App\Models\InventoryHistory;
use App\Models\ShipmentScan;

class ProcessCompletedOrders extends Command
{
    protected $signature = 'orders:process-completed';
    protected $description = 'Process completed orders and update product quantities';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Start a database transaction
        DB::beginTransaction();
        try {
            // Get all completed orders
            $completedOrders = AuxpackingOrder::where('status', 3)->get();
            foreach ($completedOrders as $order) {
                // Get all containers for the order
                $containers = AuxpackingContainer::where('order_id', $order->order_id)->where('status', 1)->get();
                foreach ($containers as $container) {
                    // Update product quantity
                    $product = Product::find($container->product_api_id);
                    if ($product) {
                        $product->quantity -= $container->quantity;
                        $product->save();
                        // Record product transaction
                        ProductTransaction::create([
                            'product_api_id' => $product->product_api_id,
                            'quantity' => $container->quantity,
                            'order_id' => $order->order_id,
                            'branch_id' => $order->branch_id,
                            'type' => 2,
                        ]);
                    }
                    // Update container quantity
                    $containerModel = Container::find($container->container_id);
                    if ($containerModel) {
                        // Record inventory transaction
                        $transaction = InventoryTransaction::create([
                            'product_id' => $product->product_id,//product_api_id
                            'container_id' => $containerModel->id,
                            'branch_id' => $containerModel->branch_id,
                            'user_id' => 2,
                            'transaction_type_id' => 2,//xuất bán
                            'type' => 2,//Xuất
                            'quantity' => $container->quantity,
                        ]);

                        // Record inventory history
                        InventoryHistory::create([
                            'transaction_id' => $transaction->id,
                            'quantity_before' => $containerModel->product_quantity,
                            'quantity_after' => $containerModel->product_quantity - $container->quantity,
                        ]);
                        $containerModel->product_quantity -= $container->quantity;
                        $containerModel->save();
                    }
                }
                // Save auxpacking_scans to shipment_scans before deleting
                $auxpackingScan = AuxpackingScan::where('order_id', $order->order_id)->first();
                if ($auxpackingScan) {
                    ShipmentScan::create([
                        'branch_id' => $auxpackingScan->branch_id,
                        'platform_id' => $auxpackingScan->platform_id,
                        'user_id' => $auxpackingScan->user_id,
                        'order_id' => $auxpackingScan->order_id,
                        'tracking_number' => $auxpackingScan->tracking_number,
                        'scan_date' => $auxpackingScan->created_at,
                    ]);
                    $auxpackingScan->delete();
                }
                // Delete the order (cascade deletes auxpacking_products and auxpacking_containers)
                $order->delete();
            }
            // Commit the transaction
            DB::commit();
            $this->info('Completed orders processed successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction if there is any error
            DB::rollback();
            // Log the error
            Log::error('Failed to process completed orders: ' . $e->getMessage(), [
                'exception' => $e,
                'order_id' => isset($order) ? $order->id : null,
            ]);
            $this->error('Failed to process completed orders: ' . $e->getMessage());
        }
    }
}
