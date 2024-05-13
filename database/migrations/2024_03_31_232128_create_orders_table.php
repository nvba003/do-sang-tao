<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code')->unique();
            $table->unsignedMediumInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->unsignedInteger('customer_account_id')->nullable();
            $table->foreign('customer_account_id')->references('id')->on('customer_accounts')->onDelete('set null');
            $table->unsignedTinyInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedTinyInteger('platform_id')->nullable();
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('set null');
            $table->unsignedMediumInteger('discount')->default(0);//đ
            $table->unsignedMediumInteger('total_amount')->default(0);//đ
            $table->string('source_info')->nullable();//thông tin về nguồn gốc đơn hàng
            $table->text('notes')->nullable();
            $table->boolean('is_cancelled_or_returned')->default(false);
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
        Schema::dropIfExists('orders');
    }
}
