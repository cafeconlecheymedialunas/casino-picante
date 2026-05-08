<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('agent_permissions');
    }

    public function down(): void
    {
        if (! Schema::hasTable('agent_permissions')) {
            Schema::create('agent_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
                $table->string('section');
                $table->string('level');
                $table->unique(['agent_id', 'section']);
            });
        }
    }
};
