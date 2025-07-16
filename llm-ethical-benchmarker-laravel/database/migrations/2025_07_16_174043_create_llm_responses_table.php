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
        Schema::create('llm_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_run_id')->constrained('test_runs');
            $table->string('provider');
            $table->string('model');
            $table->float('temperature')->nullable();
            $table->text('prompt');
            $table->longText('response_raw');
            $table->decimal('cost_usd', 8, 4)->nullable();
            $table->integer('latency_ms')->nullable();
            $table->json('scores')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llm_responses');
    }
};
