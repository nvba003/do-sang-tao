<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name'); // Tên chi nhánh
            $table->string('address')->nullable(); // Địa chỉ
            $table->string('phone')->nullable(); // Số điện thoại liên hệ
            $table->string('email')->nullable(); // Email liên hệ
            $table->text('notes')->nullable(); // Ghi chú hoặc thông tin bổ sung về chi nhánh
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
        Schema::dropIfExists('branches');
    }
}
