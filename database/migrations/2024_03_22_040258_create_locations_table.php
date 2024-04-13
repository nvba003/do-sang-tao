<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('location_name')->nullable(); // Mã vị trí (nếu là cha) hoặc mã số thùng (nếu là con)
            $table->text('description')->nullable(); // Mô tả chi tiết về vị trí
            $table->unsignedSmallInteger('parent_id')->nullable(); // Cột này cho phép NULL cho menu cha
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
        Schema::dropIfExists('locations');
    }
}
