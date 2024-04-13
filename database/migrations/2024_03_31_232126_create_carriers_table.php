<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name'); // Tên đơn vị vận chuyển
            $table->string('code')->unique(); // Mã đơn vị vận chuyển, dùng để nhận diện một cách nhanh chóng
            $table->text('description')->nullable(); // Mô tả về đơn vị vận chuyển
            $table->string('contact_info')->nullable(); // Thông tin liên hệ, có thể là số điện thoại hoặc email
            $table->string('tracking_url')->nullable(); // URL dùng để theo dõi đơn hàng trên trang của đơn vị vận chuyển
            $table->timestamps(); // Dấu thời gian khi tạo và cập nhật bản ghi
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carriers');
    }
}
