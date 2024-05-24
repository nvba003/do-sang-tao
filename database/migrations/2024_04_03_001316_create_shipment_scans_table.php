<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_scans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string('tracking_number');
            $table->boolean('scanned')->default(false);
            $table->timestamp('scan_date')->nullable();
            $table->timestamps();

            $table->unsignedMediumInteger('user_id')->nullable(); // ID của người dùng thực hiện thao tác, nullable nếu hành động được thực hiện bởi hệ thống
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipment_scans');
    }
}
