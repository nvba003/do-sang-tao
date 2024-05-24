<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTikiDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_tiki_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_tiki_id');
            $table->foreign('order_tiki_id')->references('id')->on('order_tikis')->onDelete('cascade');
            $table->unsignedInteger('order_detail_id')->nullable();
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onDelete('set null');
            $table->unsignedTinyInteger('serial')->nullable();//STT danh sách sản phẩm
            $table->string('sku')->nullable();
            $table->unsignedInteger('product_api_id')->nullable();
            $table->foreign('product_api_id')->references('product_api_id')->on('products')->onDelete('set null');
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('quantity')->nullable();
            $table->unsignedMediumInteger('price')->nullable();
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
        Schema::dropIfExists('order_tiki_details');
    }
}
