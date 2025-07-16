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
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('persona_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('prompt_template')->nullable();
            $table->boolean('is_multiple_choice')->default(false);
            $table->json('choices')->nullable();
            $table->integer('revision')->default(1);
            $table->string('md5_hash', 32)->nullable();
            $table->timestamps();

            $table->foreign('persona_id')->references('id')->on('personas')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }
};
