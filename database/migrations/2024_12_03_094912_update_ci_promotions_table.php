<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCiPromotionsTable extends Migration
{
    public function up()
    {
        Schema::table('ci_promotions', function (Blueprint $table) {
            $table->renameColumn('discount_percent', 'discount');
            $table->integer('quantity')->default(0)->after('end_date'); // Số lượng mã
            $table->text('description')->nullable()->after('promo_name'); // Mô tả
        });
    }

    public function down()
    {
        Schema::table('ci_promotions', function (Blueprint $table) {
            $table->renameColumn('discount', 'discount_percent');
            $table->dropColumn('quantity');
            $table->dropColumn('description');
        });
    }
}
