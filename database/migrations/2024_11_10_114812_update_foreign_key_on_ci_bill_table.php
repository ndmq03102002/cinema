<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeyOnCiBillTable extends Migration
{
    /**
     * Thực thi migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ci_bill', function (Blueprint $table) {
            $table->dropForeign(['account_id']);

            $table->foreign('account_id')->references('id')->on('ci_account');
        });
    }

    /**
     * Lùi migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ci_bill', function (Blueprint $table) {
            $table->dropForeign(['account_id']);

            $table->foreign('account_id')->references('id')->on('ci_bill');
        });
    }
}
