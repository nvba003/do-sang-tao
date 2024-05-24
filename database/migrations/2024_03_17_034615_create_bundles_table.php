<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundles', function (Blueprint $table) {//chi tiết sản phẩm bundle
            $table->smallIncrements('id');
            $table->string('name')->nullable();
            $table->unsignedMediumInteger('price')->default(0);
            $table->unsignedTinyInteger('type')->nullable()->comment('1:one product, 2:multiple products');//1: sản phẩm với nhiều số lượng, 2: nhiều sản phẩm với số lượng khác nhau
            $table->text('description')->nullable();
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
        Schema::dropIfExists('bundles');
    }
}
