<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->decimal('quality_rating', 3, 2);
            $table->decimal('price_rating', 3, 2);
            $table->decimal('delivery_time_rating', 3, 2);
            $table->decimal('customer_service_rating', 3, 2);
            $table->decimal('supply_capability_rating', 3, 2);
            $table->decimal('reliability_rating', 3, 2);
            $table->decimal('warranty_policy_rating', 3, 2);
            $table->decimal('overall_rating', 3, 2)->nullable();
            $table->unsignedInteger('note_id')->nullable();
            $table->foreign('note_id')->references('id')->on('supplier_notes')->onDelete('set null');
            $table->date('review_date');
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
        Schema::dropIfExists('supplier_reviews');
    }
}
