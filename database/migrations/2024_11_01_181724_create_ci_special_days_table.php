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
        Schema::create('ci_special_days', function (Blueprint $table) {
            $table->id();
            $table->enum('day_type', ['weekend', 'holiday', 'promotion']);
            $table->string('description')->nullable();
            $table->date('special_day'); // Ngày áp dụng đặc biệt
            $table->integer('extra_fee')->default(0); // Lưu phụ thu dưới dạng số nguyên
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
        Schema::dropIfExists('ci_special_days');
    }
};
