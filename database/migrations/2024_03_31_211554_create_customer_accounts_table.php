<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_accounts', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedMediumInteger('customer_id')->nullable();// Không link với bảng Customer
            $table->unsignedTinyInteger('platform_id')->nullable();
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('set null');
            $table->string('account_name')->unique();
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
        Schema::dropIfExists('customer_accounts');
    }
}
