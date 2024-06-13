<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundle_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('bundle_id');
            $table->foreign('bundle_id')->references('id')->on('bundles')->onDelete('cascade');
            $table->unsignedInteger('product_api_id');
            $table->foreign('product_api_id')->references('product_api_id')->on('products'); // Khóa ngoại tham chiếu đến Products
            $table->decimal('quantity', 8, 2);
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
        Schema::dropIfExists('bundle_items');
    }
}
