<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->unsignedInteger('related_task_id'); // Công việc liên quan
            $table->foreign('related_task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->unsignedTinyInteger('relation_type'); // Loại quan hệ, ví dụ: '1:dependency', '2:related'
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
        Schema::dropIfExists('task_relations');
    }
}
