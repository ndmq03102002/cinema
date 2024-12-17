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
        Schema::create('ci_movie', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('genre_id');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('rated_id');
            $table->string('avatar');
            $table->string('poster');
            $table->string('trailer_url');
            $table->string('duration');
            $table->date('date');
            $table->string('performer')->nullable();
            $table->string('director')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ci_movie');
    }
};
