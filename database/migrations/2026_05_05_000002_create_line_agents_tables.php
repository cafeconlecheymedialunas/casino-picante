<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['encargado', 'miembro'])->default('miembro');
            $table->boolean('is_active')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->timestamps();
            $table->unique(['line_id', 'agent_id']);
        });

        Schema::create('line_agent_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->string('permission'); // resource.action, e.g. "promo.create"
            $table->unique(['line_id', 'agent_id', 'permission'], 'lap_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_agent_permissions');
        Schema::dropIfExists('line_agents');
    }
};
