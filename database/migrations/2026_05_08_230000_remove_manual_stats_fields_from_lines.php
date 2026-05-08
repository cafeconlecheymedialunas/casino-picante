<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn([
                'mejor_mes',
                'mejor_mes_total',
                'mejor_plataforma',
                'mejor_plataforma_total',
                'ventas_mes_actual',
                'ventas_mes_pasado',
                'ventas_mes_antiguo',
                'ganancia_encargado',
                'porcentaje_encargado',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->string('mejor_mes')->nullable();
            $table->decimal('mejor_mes_total', 12, 2)->nullable();
            $table->string('mejor_plataforma')->nullable();
            $table->decimal('mejor_plataforma_total', 12, 2)->nullable();
            $table->decimal('ventas_mes_actual', 12, 2)->nullable();
            $table->decimal('ventas_mes_pasado', 12, 2)->nullable();
            $table->decimal('ventas_mes_antiguo', 12, 2)->nullable();
            $table->decimal('ganancia_encargado', 12, 2)->nullable();
            $table->decimal('porcentaje_encargado', 5, 2)->nullable();
        });
    }
};
