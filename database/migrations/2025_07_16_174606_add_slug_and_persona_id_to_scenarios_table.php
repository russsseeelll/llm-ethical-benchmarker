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
        Schema::table('scenarios', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->unsignedBigInteger('persona_id')->nullable()->after('id');
            $table->foreign('persona_id')->references('id')->on('personas')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->dropColumn(['slug', 'persona_id']);
        });
    }
};
