<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fund_source_id');
            $table->foreign('fund_source_id')->references('id')->on('fund_sources')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('transaction_date');
            $table->unsignedTinyInteger('exchange_rate');
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
        Schema::dropIfExists('fund_transactions');
    }
}
