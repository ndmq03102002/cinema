<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ci_bill', function (Blueprint $table) {
            $table->integer('staff_check')->after('cinema_id'); // Thay 'last_column_name' bằng tên cột trước đó
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ci_bill', function (Blueprint $table) {
            $table->dropColumn('staff_check');
        });
    }
};
