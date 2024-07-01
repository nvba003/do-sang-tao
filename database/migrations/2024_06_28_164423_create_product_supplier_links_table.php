<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSupplierLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_supplier_links', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_api_id');
            $table->foreign('product_api_id')->references('product_api_id')->on('products')->onDelete('cascade');
            $table->unsignedInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->unsignedInteger('supplier_product_id')->nullable();
            $table->unsignedInteger('supplier_product_sku_id')->nullable();
            $table->foreign('supplier_product_id')->references('id')->on('supplier_products')->onDelete('set null');
            $table->foreign('supplier_product_sku_id')->references('id')->on('supplier_product_skus')->onDelete('set null');
            $table->unsignedInteger('supplier_group_id')->nullable();
            $table->foreign('supplier_group_id')->references('id')->on('supplier_groups');
            $table->text('url')->nullable();
            $table->unsignedTinyInteger('available')->default(true);//còn hàng, hết hàng, ncc xóa,...
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
        Schema::dropIfExists('product_supplier_links');
    }
}
