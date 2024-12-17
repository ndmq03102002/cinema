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
        Schema::create('ci_time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_name', 50);
            $table->time('start_time');
            $table->time('end_time');
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
        Schema::dropIfExists('ci_time_slots');
    }
};
