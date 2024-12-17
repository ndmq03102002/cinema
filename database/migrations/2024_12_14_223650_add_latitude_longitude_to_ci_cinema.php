<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatitudeLongitudeToCiCinema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ci_cinema', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('address'); // Thay 'column_name' bằng cột hiện có mà bạn muốn thêm sau
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
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
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
}
