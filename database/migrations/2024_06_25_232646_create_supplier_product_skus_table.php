<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_product_skus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_product_id');
            $table->foreign('supplier_product_id')->references('id')->on('supplier_products')->onDelete('cascade');
            $table->string('sku_id')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('prop_id')->nullable();
            $table->string('prop_value')->nullable();
            $table->unsignedMediumInteger('storage')->nullable();
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
        Schema::dropIfExists('supplier_product_skus');
    }
}
