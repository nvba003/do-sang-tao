<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_cart_id');
            $table->foreign('purchase_cart_id')->references('id')->on('purchase_carts')->onDelete('cascade');
            $table->unsignedInteger('product_api_id')->nullable();
            $table->foreign('product_api_id')->references('product_api_id')->on('products')->onDelete('set null');
            $table->unsignedInteger('supplier_product_sku_id')->nullable();
            $table->foreign('supplier_product_sku_id')->references('id')->on('supplier_product_skus')->onDelete('set null');
            $table->unsignedSmallinteger('quantity')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->text('notes')->nullable(); // Ghi chú
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
        Schema::dropIfExists('purchase_cart_items');
    }
}
