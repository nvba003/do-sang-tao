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
            $table->unsignedTinyInteger('order_type_id')->default(1)->comment('1:regular, 2:promotion, 3:wholesale, 4:custom');
            $table->foreign('order_type_id')->references('id')->on('order_types');
            $table->text('source_info')->nullable();//thông tin về nguồn gốc đơn hàng
            $table->unsignedInteger('subtotal')->default(0);//đ
            $table->decimal('discount_percent', 5, 2)->default(0.00);//==============
            $table->unsignedInteger('total_discount')->default(0);//đ
            $table->unsignedMediumInteger('shipping_fee')->default(0); // Phí giao hàng
            $table->unsignedMediumInteger('customer_shipping_fee')->default(0); // Phí giao hàng khách trả
            $table->unsignedInteger('total_amount')->default(0);//đ
            $table->decimal('tax', 5, 2)->default(0.00); // % Thuế
            $table->unsignedMediumInteger('commission_fee')->default(0); // Phí hoa hồng
            $table->unsignedInteger('final_amount')->default(0);//đ
            $table->unsignedTinyInteger('payment')->default(1)->comment('1:chưa, 2:chưa đủ, 3:đủ, 4:dư');
            $table->text('notes')->nullable();
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
