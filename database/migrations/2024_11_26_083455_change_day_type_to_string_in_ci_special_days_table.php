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
        Schema::table('ci_special_days', function (Blueprint $table) {
            // Đổi kiểu dữ liệu từ ENUM sang STRING
            $table->string('day_type')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ci_special_days', function (Blueprint $table) {
            // Quay lại kiểu ENUM
            $table->enum('day_type', ['weekend', 'holiday', 'promotion'])->change();
        });
    }
};
