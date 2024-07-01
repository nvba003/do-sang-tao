<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->unsignedBigInteger('num_iid')->nullable();
            $table->string('title')->nullable();
            $table->text('desc_short')->nullable()->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('orginal_price', 10, 2)->nullable();
            $table->string('nick')->nullable();
            $table->unsignedMediumInteger('num')->nullable();
            $table->string('detail_url')->nullable();
            $table->string('pic_url')->nullable();
            $table->text('desc')->nullable();
            $table->unsignedMediumInteger('min_order')->nullable();
            $table->unsignedTinyInteger('available')->default(true);//còn hàng, hết hàng, ncc xóa,...
            $table->timestamps();

            $table->index('num_iid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_products');
    }
}
