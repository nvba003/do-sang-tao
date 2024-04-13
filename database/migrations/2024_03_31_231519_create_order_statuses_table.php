<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name')->unique();//BÁN HÀNG, GIAO HÀNG, HOÀN ĐƠN, Tư vấn, KH cần xem lại
            $table->unsignedTinyInteger('parent_id')->nullable(); // Thêm cột parent_id
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('order_statuses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_statuses');
    }
}
