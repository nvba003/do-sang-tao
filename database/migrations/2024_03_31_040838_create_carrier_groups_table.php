<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_groups', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name'); // Tên đơn vị vận chuyển
            $table->text('description')->nullable(); // Mô tả về đơn vị vận chuyển
            $table->string('contact_info')->nullable(); // Thông tin liên hệ, có thể là số điện thoại hoặc email
            $table->string('tracking_url')->nullable(); // URL dùng để theo dõi đơn hàng trên trang của đơn vị vận chuyển
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
        Schema::dropIfExists('carrier_groups');
    }
}
