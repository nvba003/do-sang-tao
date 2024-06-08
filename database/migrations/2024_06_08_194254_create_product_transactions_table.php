<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_api_id');
            $table->foreign('product_api_id')->references('product_api_id')->on('products')->onDelete('cascade');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedTinyInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->unsignedTinyInteger('type')->comment('1: Nhap, 2: Xuat');
            $table->decimal('quantity', 8, 2); // Số lượng sản phẩm
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
        Schema::dropIfExists('product_transactions');
    }
}
