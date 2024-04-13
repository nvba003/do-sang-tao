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
            $table->unsignedMediumInteger('customer_account_id')->nullable();
            $table->foreign('customer_account_id')->references('id')->on('customer_accounts')->onDelete('set null');
            $table->unsignedTinyInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedTinyInteger('order_source_id')->nullable();
            $table->foreign('order_source_id')->references('id')->on('order_sources')->onDelete('set null');
            $table->unsignedMediumInteger('discount')->default(0);//đ
            $table->unsignedMediumInteger('total_amount')->default(0);//đ
            $table->string('source_link')->nullable();//đường dẫn đến đơn gốc
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
