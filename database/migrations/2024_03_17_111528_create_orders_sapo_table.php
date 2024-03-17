<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersSapoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_sapo', function (Blueprint $table) {
            $table->id();
            $table->string('madonhang')->unique();
            $table->string('tenkhachhang');
            $table->string('sdt');
            $table->text('diachi');
            $table->string('chinhanh');
            $table->string('nguon');
            $table->string('sanpham');
            $table->decimal('tongtien', 8, 2); // 8 là số tổng số chữ số, 2 là số chữ số sau dấu phẩy
            $table->timestamps(); // Tạo ra các cột `created_at` và `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders_sapo');
    }
}
