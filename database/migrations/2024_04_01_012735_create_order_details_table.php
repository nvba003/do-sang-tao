<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedInteger('product_api_id');
            $table->foreign('product_api_id')->references('product_api_id')->on('products'); // Khóa ngoại tham chiếu đến Products
            $table->decimal('quantity', 8, 2)->default(0); // Số lượng sản phẩm trong đơn hàng
            $table->unsignedMediumInteger('price')->default(0);// Giá của sản phẩm tại thời điểm đặt hàng
            $table->unsignedMediumInteger('total')->default(0); // Tổng giá trị (có thể tính là quantity * price)
            $table->timestamps(); // Thời gian tạo và cập nhật bản ghi
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
