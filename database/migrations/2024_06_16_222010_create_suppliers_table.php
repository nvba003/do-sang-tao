<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_group_id');
            $table->foreign('supplier_group_id')->references('id')->on('supplier_groups');
            $table->unsignedInteger('supplier_level_detail_id')->nullable();
            $table->foreign('supplier_level_detail_id')->references('id')->on('supplier_level_details')->onDelete('set null');
            $table->string('name');
            $table->text('link')->nullable();
            $table->text('contact_info')->nullable();
            $table->decimal('average_rating', 2, 1)->nullable();
            $table->integer('years_established')->nullable(); // Số năm thành lập
            $table->string('business_type')->nullable(); // Loại hình kinh doanh (nhà máy sản xuất, cửa hàng, ...)
            $table->decimal('product_description_accuracy', 4, 2)->nullable(); // Độ tin cậy của mô tả sản phẩm
            $table->decimal('response_speed', 4, 2)->nullable(); // Tốc độ phản hồi
            $table->decimal('shipping_speed', 4, 2)->nullable(); // Tốc độ giao hàng
            $table->decimal('customer_return_rate', 4, 2)->nullable(); // Tỷ lệ khách hàng quay lại
            $table->string('business_mode')->nullable(); // Chế độ kinh doanh
            $table->string('customer_service')->nullable(); // Dịch vụ khách hàng
            $table->decimal('quality_rating', 4, 2)->nullable(); // Điểm chất lượng sản phẩm
            $table->decimal('logistics_rating', 4, 2)->nullable(); // Điểm tốc độ giao hàng
            $table->decimal('service_rating', 4, 2)->nullable(); // Điểm dịch vụ
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
        Schema::dropIfExists('suppliers');
    }
}
