<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCiPromotionUserTable extends Migration
{
    public function up()
    {
        Schema::create('ci_promotion_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('ci_promotions')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('ci_account')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ci_promotion_user');
    }
}

