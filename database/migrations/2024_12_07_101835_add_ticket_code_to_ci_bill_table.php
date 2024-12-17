<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketCodeToCiBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ci_bill', function (Blueprint $table) {
            $table->string('ticket_code')->after('id')->comment('Unique ticket code for each bill');
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
            $table->dropColumn('ticket_code');
        });
    }
}
