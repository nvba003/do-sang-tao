<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierLevelDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_level_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_level_id');
            $table->foreign('supplier_level_id')->references('id')->on('supplier_levels')->onDelete('cascade');
            $table->string('detail_name')->nullable(); // Tên chi tiết cấp độ, ví dụ: 1 sao, 2 sao, Kim cương, Vương miện
            $table->text('detail_description')->nullable(); // Mô tả chi tiết cấp độ
            $table->unsignedTinyinteger('rank')->nullable(); // Thứ hạng trong cấp độ, ví dụ: 1, 2, 3, v.v.
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
        Schema::dropIfExists('supplier_level_details');
    }
}
