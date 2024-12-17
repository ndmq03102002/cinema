<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToMultipleTables extends Migration
{
    public function up()
    {
        Schema::table('ci_account', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('ci_profile', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('ci_bill', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('ci_cinema', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_country', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_foods', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_genre', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_movie', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_movie_genre', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_movie_show_time', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_promotions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_rated', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_room', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_seat_type', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_special_days', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_time_slots', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_weekly_ticket_prices', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('ci_ticket', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_evaluate', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('ci_promotion_user', function (Blueprint $table) {
            $table->softDeletes();
        });
       
    }

    public function down()
    {
        Schema::table('ci_account', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('ci_profile', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('ci_bill', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('ci_cinema', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_country', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_foods', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_genre', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_movie', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_movie_genre', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_movie_show_time', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_promotions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_rated', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_room', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_seat_type', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_special_days', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_time_slots', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_weekly_ticket_prices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('ci_ticket', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_evaluate', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ci_promotion_user', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
    }
}