<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderShopeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shopees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('order_code')->unique();
            $table->timestamp('order_date')->nullable();
            $table->string('customer_account')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->unsignedMediumInteger('total_amount')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->unsignedTinyInteger('platform_id')->nullable();
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('set null');
            $table->unsignedInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
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
        Schema::dropIfExists('order_shopees');
    }
}
