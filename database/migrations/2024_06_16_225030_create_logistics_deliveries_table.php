<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogisticsDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistics_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('logistics_id');
            $table->foreign('logistics_id')->references('id')->on('logistics')->onDelete('cascade');
            $table->date('delivery_date');
            $table->text('issues')->nullable();
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
        Schema::dropIfExists('logistics_deliveries');
    }
}
