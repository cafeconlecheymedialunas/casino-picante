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
        Schema::create('line_platform', function (Blueprint $table) {
            $table->foreignId('line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->text('custom_message')->nullable(); // mensaje personalizado por línea
            $table->boolean('is_active')->default(true); // si esta línea usa esta plataforma
            $table->primary(['line_id', 'platform_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_platform');
    }
};
