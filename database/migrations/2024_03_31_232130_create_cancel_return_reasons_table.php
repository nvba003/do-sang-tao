<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancelReturnReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancel_return_reasons', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('reason'); // Lý do hủy hoặc trả hàng
            $table->enum('type', ['Huy', 'Tra']); // Loại: hủy đơn hoặc trả hàng
            $table->text('description')->nullable(); // Mô tả chi tiết về lý do (nếu cần)
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
        Schema::dropIfExists('cancel_return_reasons');
    }
}
