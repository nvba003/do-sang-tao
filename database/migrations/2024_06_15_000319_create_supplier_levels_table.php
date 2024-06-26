<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // Tên cấp độ, ví dụ: Sao, Kim cương, Vương miện
            $table->text('description')->nullable(); // Mô tả cấp độ
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
        Schema::dropIfExists('supplier_levels');
    }
}
