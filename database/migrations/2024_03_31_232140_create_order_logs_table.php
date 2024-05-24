<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_process_id')->nullable();//có thể là NULL cho các trường hợp như tạo mới
            $table->foreign('order_process_id')->references('id')->on('order_processes')->onDelete('set null');
            $table->unsignedMediumInteger('user_id')->nullable(); // ID của người dùng thực hiện thao tác, nullable nếu hành động được thực hiện bởi hệ thống
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedTinyInteger('type'); // Loại hành động: '1:create', '2:update', '3:delete', '4:change product',...
            $table->text('description')->nullable(); // Mô tả chi tiết về thao tác, có thể bao gồm thông tin cũ và mới của dữ liệu
            $table->json('changes')->nullable(); // Các thay đổi được lưu trữ dưới dạng JSON, chứa thông tin cũ và mới
            $table->timestamps(); // Thời gian thao tác được thực hiện           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_logs');
    }
}
