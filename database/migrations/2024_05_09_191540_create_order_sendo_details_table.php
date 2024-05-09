<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSendoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_sendo_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_sendo_id');
            $table->foreign('order_sendo_id')->references('id')->on('order_sendos')->onDelete('cascade');
            $table->string('sku')->nullable();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('quantity')->nullable();
            $table->unsignedMediumInteger('price')->nullable();
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
        Schema::dropIfExists('order_sendo_details');
    }
}
