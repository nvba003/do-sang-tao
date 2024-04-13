<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('inventory_transactions'); // Liên kết với InventoryTransactions
            $table->decimal('quantity_before', 8, 2)->nullable(); // Số lượng trước giao dịch
            $table->decimal('quantity_after', 8, 2)->nullable(); // Số lượng sau giao dịch
            $table->text('notes')->nullable(); // Ghi chú về thay đổi
            $table->decimal('expected_quantity', 8, 2)->nullable(); // Số lượng dự kiến
            $table->decimal('actual_quantity', 8, 2)->nullable(); // Số lượng thực tế
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
        Schema::dropIfExists('inventory_histories');
    }
}
