<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status');//'not_started', 'in_progress', 'blocked', 'completed'
            $table->unsignedTinyInteger('category_id');//phân loại như Cá nhân, nhập hàng, KH đặt,..
            $table->foreign('category_id')->references('id')->on('task_categories');
            $table->string('task_code')->unique()->nullable();//mã công việc được tạo tự động dựa trên phân loại.
            $table->unsignedTinyInteger('outcome')->nullable();// 'success', 'failure', 'pending_customer_response', 'abandoned'
            $table->dateTime('customer_contact_date')->nullable();//ngày liên hệ khách hàng cuối cùng
            $table->dateTime('customer_response_date')->nullable();//ngày nhận được phản hồi từ khách hàng
            $table->dateTime('due_date')->nullable();//ngày hết hạn của công việc
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
        Schema::dropIfExists('tasks');
    }
}
