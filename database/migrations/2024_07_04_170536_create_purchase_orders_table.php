<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->unsignedInteger('fund_id')->nullable();
            $table->foreign('fund_id')->references('id')->on('fund_usage_logs')->onDelete('set null');
            $table->unsignedInteger('supplier_delivery_id')->nullable();
            $table->foreign('supplier_delivery_id')->references('id')->on('supplier_deliveries')->onDelete('set null');
            $table->unsignedInteger('logistics_delivery_id')->nullable();
            $table->foreign('logistics_delivery_id')->references('id')->on('logistics_deliveries')->onDelete('set null');
            $table->decimal('exchange_rate', 10, 2)->nullable(); // Tỷ giá lúc mua hàng
            $table->decimal('total_price_cny', 10, 2); // Tổng giá tiền theo tệ
            $table->decimal('shipping_fee_cny', 10, 2)->nullable(); // Phí vận chuyển của supplier
            $table->text('order_link')->nullable(); // Link xem đơn hàng
            $table->date('purchase_date');
            $table->date('supplier_delivery_date')->nullable();
            $table->date('logistics_receive_date_cn')->nullable();
            $table->date('logistics_delivery_date_vn')->nullable();
            $table->date('warehouse_receive_date_vn')->nullable();
            $table->date('final_delivery_date')->nullable();
            $table->string('issue_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
