<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStartEndTimeColumnsInCiMovieShowTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ci_movie_show_time', function (Blueprint $table) {
            $table->string('start_time')->change();
            $table->string('end_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // xin chao 123
    public function down()
    {
        Schema::table('ci_movie_show_time', function (Blueprint $table) {
            $table->time('start_time')->change();
            $table->time('end_time')->change();

        });
    }
}

