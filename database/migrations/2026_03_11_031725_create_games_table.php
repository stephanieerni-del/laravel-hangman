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

       Schema::create('level_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('level')->unique();
            $table->string('difficulty', 10);
            $table->unsignedTinyInteger('x');
            $table->unsignedTinyInteger('y');
            $table->timestamps();
        });
        
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->foreignId('level_point_id')->nullable()->constrained()->unique();
            $table->foreignId('user_id')->constrained();
            $table->string('name', 30)->unique();
            $table->tinyInteger('starting_lives')->unsigned()->default(6);
            $table->timestamps();
        });

        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('game_id')->constrained();
            $table->string('category');
            $table->string('word');
            $table->timestamps();
        });

        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('game_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->boolean('is_active')->default(true);
            $table->integer('score')->default(0);
            $table->timestamps();
        });

        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained();
            $table->foreignId('challenge_id')->constrained();
            $table->json('guesses')->nullable();
            $table->json('correct_guesses')->nullable();
            $table->boolean('is_skipped')->default(false);
            $table->timestamps();
        });

     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_points');
        Schema::dropIfExists('stages');
        Schema::dropIfExists('players');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('games');
    }
};
