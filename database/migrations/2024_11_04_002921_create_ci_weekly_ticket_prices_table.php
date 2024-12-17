<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCiWeeklyTicketPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ci_weekly_ticket_prices', function (Blueprint $table) {
            $table->id(); // Tạo cột id tự động tăng
            $table->string('name'); // Tên vé
            $table->string('description')->nullable(); // Mô tả vé, có thể null
            $table->integer('extra_fee')->default(0); // Phí thêm, mặc định là 0
            $table->timestamps(); // Tạo các cột created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ci_weekly_ticket_prices');
    }
}
