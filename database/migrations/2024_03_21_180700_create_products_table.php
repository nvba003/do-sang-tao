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
            $table->tinyInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->text('description')->nullable();
            $table->unsignedMediumInteger('base_price')->default(0);//đ
            $table->boolean('is_bundle')->default(false);
            $table->tinyInteger('bundle_type_id')->unsigned()->nullable();
            $table->foreign('bundle_type_id')->references('id')->on('bundle_types')->onDelete('set null');
            $table->unsignedSmallInteger('reorder_level')->nullable();
            $table->decimal('length', 5, 2)->nullable();//cm
            $table->decimal('width', 5, 2)->nullable();//cm
            $table->decimal('height', 5, 2)->nullable();//cm
            $table->unsignedSmallInteger('weight')->nullable();//gram
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
