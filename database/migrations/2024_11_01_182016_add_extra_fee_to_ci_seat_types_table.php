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
        Schema::table('ci_seat_type', function (Blueprint $table) {
            $table->integer('extra_fee')->default(0); // Thêm cột extra_fee
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ci_seat_type', function (Blueprint $table) {
            $table->dropColumn('extra_fee');
        });
    }
};
