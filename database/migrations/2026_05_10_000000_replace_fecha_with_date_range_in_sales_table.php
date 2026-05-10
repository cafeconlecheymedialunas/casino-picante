<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('platform_id');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['fecha']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->date('fecha')->nullable()->after('platform_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin']);
        });
    }
};