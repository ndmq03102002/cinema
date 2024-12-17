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
        Schema::table('ci_account', function (Blueprint $table) {
            $table->renameColumn('chi_nhanh', 'cinema_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ci_account', function (Blueprint $table) {
            $table->renameColumn('cinema_id', 'chi_nhanh');
        });
    }
};
