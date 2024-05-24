<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuxpackingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auxpacking_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('auxpacking_order_id');
            $table->foreign('auxpacking_order_id')->references('id')->on('auxpacking_orders')->onDelete('cascade');
            $table->unsignedTinyInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedTinyInteger('platform_id')->nullable();
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('set null');
            $table->unsignedInteger('product_api_id');
            $table->foreign('product_api_id')->references('product_api_id')->on('products')->onDelete('cascade');
            $table->decimal('quantity', 8, 2);//số lượng cần đóng gói
            $table->unsignedTinyInteger('status')->default(1);//1:mặc định, 2:lấy chưa đủ, 3:lấy đủ, (4:thiếu sp trong thùng)
            $table->text('notes')->nullable();//ghi chú về sản phẩm như: lựa sản phẩm đẹp, lấy thêm sp trong đơn ncc
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
        Schema::dropIfExists('auxpacking_products');
    }
}
