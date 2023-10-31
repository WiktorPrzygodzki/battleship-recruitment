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
        Schema::create('game', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_1_id');
            $table->foreign('player_1_id')->references('id')->on('players');
            $table->unsignedBigInteger('player_2_id');
            $table->foreign('player_2_id')->references('id')->on('players');
            $table->string('winner');
            $table->integer('available_slots');
            $table->string('status');
            $table->unsignedBigInteger('current_player_id');
            $table->foreign('current_player_id')->references('id')->on('players');
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game');
    }
};
