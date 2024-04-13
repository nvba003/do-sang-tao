<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedTinyInteger('status_id')->nullable();//trạng thái xử lý và giao hàng
            $table->foreign('status_id')->references('id')->on('order_statuses')->onDelete('set null');
            $table->unsignedMediumInteger('responsible_user_id')->nullable(); // Người dùng phụ trách
            $table->foreign('responsible_user_id')->references('id')->on('users'); // Khóa ngoại tham chiếu đến bảng Users
            $table->text('notes')->nullable();
            $table->timestamp('approval_time')->nullable();//thời gian chấp thuận (duyệt)
            $table->timestamp('packing_time')->nullable();//thời gian đóng gói
            $table->timestamp('delivery_handoff_time')->nullable();//thời gian bàn giao vận chuyển
            $table->date('completion_time')->nullable();//thời gian hoàn thành
            $table->string('tracking_number')->nullable();
            $table->date('ship_date')->nullable();//ngày đơn hàng gửi đi
            $table->date('estimated_delivery_date')->nullable();//ngày khách nhận ước tính
            $table->date('actual_delivery_date')->nullable();//ngày khách nhận thực tế
            $table->unsignedTinyInteger('carrier_id')->nullable();//đơn vị vận chuyển
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('set null');
            $table->text('shipping_notes')->nullable();
            // Các cột mới để quản lý hủy hoặc hoàn đơn
            $table->unsignedInteger('cancel_return_id')->nullable();
            $table->foreign('cancel_return_id')->references('id')->on('order_cancel_and_returns')->onDelete('set null');
            $table->date('received_return_date')->nullable();//ngày nhận đơn hoàn
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
        Schema::dropIfExists('order_processes');
    }
}
