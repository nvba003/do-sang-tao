<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuxpackingContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auxpacking_containers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedTinyInteger('platform_id')->nullable();
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('set null');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedInteger('auxpacking_product_id');
            $table->foreign('auxpacking_product_id')->references('id')->on('auxpacking_products')->onDelete('cascade');
            $table->unsignedInteger('product_api_id');
            $table->foreign('product_api_id')->references('product_api_id')->on('products')->onDelete('cascade');
            $table->unsignedSmallInteger('container_id');
            $table->foreign('container_id')->references('id')->on('containers')->onDelete('cascade');
            $table->decimal('quantity', 8, 2);//số lượng cần lấy ra
            $table->boolean('status')->default(false);//false:chưa lấy, true:đã lấy
            $table->text('notes')->nullable();//ghi chú về thùng hàng như: thùng hàng sai số lượng
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
        Schema::dropIfExists('auxpacking_containers');
    }
}
