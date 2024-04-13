<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderCancelAndReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_cancel_and_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->nullable(); // ID của đơn hàng
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->enum('type', ['Huy', 'Tra']); // Loại: hủy đơn hoặc trả hàng
            $table->unsignedTinyInteger('reason_id')->nullable();
            $table->foreign('reason_id')->references('id')->on('cancel_return_reasons')->onDelete('set null');
            $table->unsignedMediumInteger('processed_by')->nullable(); // ID của người xử lý (nhân viên)
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            $table->text('notes')->nullable(); // Ghi chú thêm về việc hủy hoặc trả hàng
            $table->timestamps(); // Thời gian tạo và cập nhật bản ghi
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_cancel_and_returns');
    }
}
