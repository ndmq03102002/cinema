<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ci_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('promo_name', 100);
            $table->integer('discount_percent')->default(0); // Lưu phần trăm giảm giá dưới dạng số nguyên
            $table->date('start_date');
            $table->date('end_date');
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
        Schema::dropIfExists('ci_promotions');
    }
};
