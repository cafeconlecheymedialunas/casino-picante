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
        Schema::table('dashboard_notifications', function (Blueprint $table) {
            $table->index('agent_id');
            $table->index(['agent_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dashboard_notifications', function (Blueprint $table) {
            $table->dropIndex(['agent_id']);
            $table->dropIndex(['agent_id', 'read_at']);
        });
    }
};
