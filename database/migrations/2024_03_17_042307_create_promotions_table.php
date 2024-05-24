<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('discount_type')->comment('1:percent, 2:fixed, 3:gift');// Loại giảm giá: phần trăm, cố định, hoặc tặng sản phẩm
            $table->decimal('discount_value', 8, 2)->nullable();//Giá trị của khuyến mãi, có thể là phần trăm, số tiền cụ thể, hoặc NULL nếu là khuyến mãi tặng sản phẩm.
            $table->unsignedInteger('gift_product_id');
            $table->foreign('gift_product_id')->references('product_api_id')->on('products'); 
            $table->decimal('minimum_quantity', 8, 2)->nullable();//Số lượng sản phẩm tối thiểu cần mua để khuyến mãi được áp dụng.
            $table->unsignedMediumInteger('minimum_amount')->nullable();//Số tiền đơn hàng tối thiểu cần đạt để khuyến mãi được áp dụng.
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('promotions');
    }
}
