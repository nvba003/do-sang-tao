<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_api_id');
            $table->foreign('product_api_id')->references('product_api_id')->on('products')->onDelete('cascade');
            $table->unsignedInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->date('purchase_date');
            $table->date('supplier_delivery_date')->nullable();
            $table->date('logistics_receive_date_cn')->nullable();
            $table->date('logistics_delivery_date_vn')->nullable();
            $table->date('warehouse_receive_date_vn')->nullable();
            $table->date('final_delivery_date')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->string('issue_status')->default('no_issues');
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
        Schema::dropIfExists('product_purchases');
    }
}
