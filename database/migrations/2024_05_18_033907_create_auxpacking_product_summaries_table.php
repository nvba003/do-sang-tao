<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuxpackingProductSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auxpacking_product_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_api_id')->nullable();
            $table->foreign('product_api_id')->references('product_api_id')->on('products');
            $table->decimal('total_quantity', 8, 2)->default(0.00);           // Tổng số lượng sản phẩm, tổng hợp từ auxpacking_products
            $table->decimal('quantity_retrieved', 8, 2)->default(0.00);        // Số lượng đã lấy từ kho, tổng hợp từ auxpacking_containers
            $table->decimal('quantity_delivered', 8, 2)->default(0.00);        // Số lượng đã giao, tổng hợp từ các sản phẩm trong auxpacking_orders đã giao
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
        Schema::dropIfExists('auxpacking_product_summaries');
    }
}
