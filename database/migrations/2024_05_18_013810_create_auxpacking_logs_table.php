<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuxpackingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auxpacking_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedTinyInteger('platform_id')->nullable();
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('set null');
            $table->unsignedMediumInteger('user_id')->nullable(); // ID của người dùng thực hiện thao tác, nullable nếu hành động được thực hiện bởi hệ thống
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedInteger('order_id')->nullable();//có thể là NULL cho các trường hợp như tạo mới
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedInteger('product_api_id')->nullable();
            $table->foreign('product_api_id')->references('product_api_id')->on('products');
            $table->unsignedSmallInteger('container_id')->nullable();
            $table->foreign('container_id')->references('id')->on('containers');
            $table->string('tracking_number')->nullable();
            $table->unsignedTinyInteger('type'); // Loại hành động: '1:create', '2:update', '3:delete', '4:change container',...
            $table->text('description')->nullable(); // Mô tả chi tiết về thao tác, có thể bao gồm thông tin cũ và mới của dữ liệu
            $table->json('changes')->nullable(); // Các thay đổi được lưu trữ dưới dạng JSON, chứa thông tin cũ và mới
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
        Schema::dropIfExists('auxpacking_logs');
    }
}
