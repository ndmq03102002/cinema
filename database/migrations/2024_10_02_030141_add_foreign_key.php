<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ci_account', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('ci_role');
        });
        Schema::table('ci_profile', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('ci_account');
        });
        Schema::table('ci_bill', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('ci_bill');
            $table->foreign('cinema_id')->references('id')->on('ci_cinema');
        });
        Schema::table('ci_movie', function (Blueprint $table) {
            $table->foreign('genre_id')->references('id')->on('ci_genre');
            $table->foreign('country_id')->references('id')->on('ci_country');
            $table->foreign('rated_id')->references('id')->on('ci_rated');
        });
        Schema::table('ci_room', function (Blueprint $table) {
            $table->foreign('cinema_id')->references('id')->on('ci_cinema');
        });
        Schema::table('ci_movie_show_time', function (Blueprint $table) {
            $table->foreign('movie_id')->references('id')->on('ci_movie');
            $table->foreign('room_id')->references('id')->on('ci_room');
        });
        Schema::table('ci_seat', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('ci_room');
            $table->foreign('seat_type_id')->references('id')->on('ci_seat_type');
        });
        Schema::table('ci_ticket', function (Blueprint $table) {
            $table->foreign('bill_id')->references('id')->on('ci_bill');
            $table->foreign('seat_id')->references('id')->on('ci_seat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
