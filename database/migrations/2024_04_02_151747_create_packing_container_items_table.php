<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingContainerItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_container_items', function (Blueprint $table) {//quản lý việc lấy hàng trong thùng để đóng gói
            $table->increments('id');
            $table->unsignedInteger('packing_list_id');
            $table->unsignedSmallInteger('container_id');
            $table->unsignedInteger('inventory_transaction_id');
            $table->decimal('quantity', 8, 2)->nullable(); // Số lượng sản phẩm được lấy từ container
            $table->text('notes')->nullable();
            $table->foreign('packing_list_id')->references('id')->on('packing_lists')->onDelete('cascade');
            $table->foreign('container_id')->references('id')->on('containers')->onDelete('cascade');
            $table->foreign('inventory_transaction_id')->references('id')->on('inventory_transactions')->onDelete('cascade');
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
        Schema::dropIfExists('packing_container_items');
    }
}
