<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('product_api_id')->on('products'); // Khóa ngoại tham chiếu đến Products
            $table->string('container_id',7); // Sử dụng kiểu string nếu khóa chính của Containers là string
            $table->foreign('container_id')->references('container_id')->on('containers')->onDelete('cascade'); // Khóa ngoại tham chiếu đến Containers
            $table->unsignedTinyInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches'); // Khóa ngoại tham chiếu đến bảng Branches
            $table->unsignedMediumInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users'); // Khóa ngoại tham chiếu đến bảng Users
            $table->unsignedTinyInteger('transaction_type_id');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->onDelete('cascade'); // Thêm khóa ngoại phân loại
            $table->enum('type', ['Nhap', 'Xuat', 'Kiem']); // Loại giao dịch: nhập kho (in) hoặc xuất kho (out) hoặc kiểm hàng SL còn thực tế (check)
            $table->decimal('quantity', 8, 2); // Số lượng sản phẩm
            $table->timestamps(); // Các trường created_at và updated_at tự động
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_transactions');
    }
}
