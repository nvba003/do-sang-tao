<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name')->unique();
            $table->string('url')->nullable();
            $table->unsignedTinyInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches'); // Khóa ngoại tham chiếu đến bảng Branches
            $table->unsignedTinyInteger('order_source_id');
            $table->foreign('order_source_id')->references('id')->on('order_sources'); 
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
        Schema::dropIfExists('platforms');
    }
}
