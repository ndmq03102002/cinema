<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvatarToCiCinemaAndCiPromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ci_cinema', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('name'); // Thêm cột avatar sau cột name
        });

        Schema::table('ci_promotions', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('promo_name'); // Thêm cột avatar sau cột title
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ci_cinema', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });

        Schema::table('ci_promotions', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
}
