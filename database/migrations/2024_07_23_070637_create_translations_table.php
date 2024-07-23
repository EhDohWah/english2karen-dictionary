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
        Schema::create('translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('word_id');
            $table->unsignedBigInteger('part_of_speech_id');
            $table->text('translation');
            $table->timestamps();
            $table->foreign('word_id')->references('id')->on('words')->onDelete('cascade');
            $table->foreign('part_of_speech_id')->references('id')->on('pos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
