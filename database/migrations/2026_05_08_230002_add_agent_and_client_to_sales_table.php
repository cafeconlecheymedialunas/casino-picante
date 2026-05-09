<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete()->after('line_id');
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete()->after('agent_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropForeign(['client_id']);
            $table->dropColumn(['agent_id', 'client_id']);
        });
    }
};