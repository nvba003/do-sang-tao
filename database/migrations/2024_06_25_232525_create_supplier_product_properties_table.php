<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierProductPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_product_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_product_id');
            $table->foreign('supplier_product_id')->references('id')->on('supplier_products')->onDelete('cascade');
            $table->string('property_id')->nullable();
            $table->string('name')->nullable();
            $table->string('value')->nullable();
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
        Schema::dropIfExists('supplier_product_properties');
    }
}
