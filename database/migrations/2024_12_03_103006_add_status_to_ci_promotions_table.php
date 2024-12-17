<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCiPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ci_promotions', function (Blueprint $table) {
            $table->integer('status')->default(0); // Hoặc bạn có thể dùng $table->enum() nếu muốn
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ci_promotions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
