<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundUsageLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_usage_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fund_transaction_id');
            $table->foreign('fund_transaction_id')->references('id')->on('fund_transactions')->onDelete('cascade');
            $table->decimal('used_amount', 10, 2);
            $table->unsignedSmallInteger('exchange_rate');
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
        Schema::dropIfExists('fund_usage_logs');
    }
}
