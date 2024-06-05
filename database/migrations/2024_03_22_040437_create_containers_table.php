<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('container_code',7)->unique();// ContainerCode
            $table->unsignedTinyInteger('container_status_id')->nullable();
            $table->foreign('container_status_id')->references('id')->on('container_statuses')->onDelete('set null'); // Add a foreign key to the ContainerStatuses table
            $table->unsignedSmallInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null'); // LocationID with foreign key constraint
            $table->unsignedInteger('product_id');//mã product_api_id
            $table->foreign('product_id')->references('product_api_id')->on('products'); // Khóa ngoại tham chiếu đến Products
            $table->decimal('product_quantity', 8, 2)->nullable(); // Số lượng sản phẩm trong thùng
            $table->string('unit')->nullable();//đơn vị
            $table->unsignedTinyInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches'); // Khóa ngoại tham chiếu đến bảng Branches
            $table->unsignedInteger('check_transaction_id')->nullable();// Không tạo khóa ngoại do inventory_transactions đã có khóa ngoại với containers
            $table->text('notes')->nullable(); // AdditionalInfo
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
        Schema::dropIfExists('containers');
    }
}
