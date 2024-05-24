<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_apis', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary(); // Sử dụng 'id' như một khóa chính không tự động tăng
            $table->string('sku');
            $table->string('name');
            $table->string('product_type')->nullable();
            $table->string('images')->nullable();
            $table->string('alias')->nullable();
            $table->decimal('inventory_quantity', 8, 2)->nullable(); // Thay đổi độ chính xác và scale nếu cần
            $table->unsignedMediumInteger('price')->nullable();//đ
            $table->unsignedSmallInteger('weight')->nullable();//gram
            $table->timestamps(); // Tự động tạo trường 'created_at' và 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_apis');
    }
}
