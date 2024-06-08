<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AuxpackingOrder;
use App\Models\AuxpackingContainer;
use App\Models\Product;
use App\Models\Container;
use App\Models\ProductTransaction;
use App\Models\InventoryTransaction;
use App\Models\InventoryHistory;

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
        // Get all completed orders
        $completedOrders = AuxpackingOrder::where('status', 3)->get();

        foreach ($completedOrders as $order) {
            // Get all containers for the order
            $containers = AuxpackingContainer::where('order_id', $order->id)->where('status', 1)->get();

            foreach ($containers as $container) {
                // Update product quantity
                $product = Product::find($container->product_api_id);
                if ($product) {
                    $product->quantity -= $container->quantity;
                    $product->save();

                    // Record product transaction
                    ProductTransaction::create([
                        'product_id' => $product->id,
                        'quantity_change' => -$container->quantity,
                        'order_id' => $order->id,
                        'note' => 'Order processed'
                    ]);
                }

                // Update container quantity
                $containerModel = Container::find($container->container_id);
                if ($containerModel) {
                    $containerModel->product_quantity -= $container->quantity;
                    $containerModel->save();

                    // Record inventory transaction
                    InventoryTransaction::create([
                        'container_id' => $containerModel->id,
                        'quantity_change' => -$container->quantity,
                        'order_id' => $order->id,
                        'note' => 'Order processed'
                    ]);

                    // Record inventory history
                    InventoryHistory::create([
                        'container_id' => $containerModel->id,
                        'quantity' => $containerModel->product_quantity,
                        'note' => 'Order processed'
                    ]);
                }
            }
        }

        $this->info('Completed orders processed successfully.');
    }
}
