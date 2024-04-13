<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainerMenuOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_menu_options', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name');
            $table->char('definition_id', 1); // Giả định mỗi định nghĩa ID chỉ có 1 ký tự
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('parent_id')->nullable(); // Cột này cho phép NULL cho menu cha
            $table->timestamps();

            // Tạo khóa ngoại, chỉ định rằng nó tham chiếu đến cùng bảng này
            $table->foreign('parent_id')->references('id')->on('container_menu_options')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container_menu_options');
    }
}
