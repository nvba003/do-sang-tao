<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('product_api_id')->on('products'); 
            $table->decimal('requested_quantity',8,2)->default(0);//Số lượng sản phẩm yêu cầu trong đơn hàng
            $table->decimal('picked_quantity',8,2)->default(0);//Số lượng sản phẩm đã được lấy từ kho để đóng gói
            $table->timestamp('packing_date')->nullable();//Ngày bắt đầu đóng gói
            $table->timestamp('completion_date')->nullable();//Ngày hoàn tất đóng gói
            $table->unsignedTinyInteger('packing_status_id');//Trạng thái đóng gói, ví dụ: "Chưa Đóng Gói", "Hoàn Thành"
            $table->foreign('packing_status_id')->references('id')->on('packing_statuses'); 
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('packing_lists');
    }
}
