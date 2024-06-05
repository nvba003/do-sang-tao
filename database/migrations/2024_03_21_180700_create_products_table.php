<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_api_id')->unique();
            $table->foreign('product_api_id')->references('id')->on('product_apis');//gàng buộc để product_api_id products không thêm được nếu product_apis không có dữ liệu
            $table->unsignedTinyInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->unsignedTinyInteger('product_group_id')->nullable();//combo, quy đổi,...
            $table->foreign('product_group_id')->references('id')->on('product_groups')->onDelete('set null');
            $table->unsignedSmallInteger('bundle_id')->nullable();//liên kết đến chi tiết sản phẩm bán theo bộ
            $table->foreign('bundle_id')->references('id')->on('bundles')->onDelete('set null');
            $table->string('sku');
            $table->string('name');
            $table->unsignedMediumInteger('base_price')->default(0);//đ
            $table->unsignedMediumInteger('price')->default(0);
            $table->decimal('quantity', 8, 2)->default(0.00);
            $table->unsignedSmallInteger('reorder_level')->nullable();
            $table->decimal('length', 5, 2)->nullable();//cm
            $table->decimal('width', 5, 2)->nullable();//cm
            $table->decimal('height', 5, 2)->nullable();//cm
            $table->unsignedSmallInteger('weight')->nullable();//gram
            $table->text('description')->nullable();
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
        Schema::dropIfExists('products');
    }
}
