<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop old unique constraint before removing columns
            $table->dropUnique(['line_id', 'platform_id', 'mes', 'anio']);

            $table->date('fecha')->nullable()->after('platform_id');
            $table->string('descripcion', 255)->nullable()->after('fecha');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['mes', 'anio', 'fecha_inicio', 'fecha_fin']);
        });

        // Backfill fecha from fecha_inicio for existing rows (if any)
        DB::statement('UPDATE sales SET fecha = date(\'now\') WHERE fecha IS NULL');

        Schema::table('sales', function (Blueprint $table) {
            $table->date('fecha')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('mes')->nullable()->after('platform_id');
            $table->integer('anio')->nullable()->after('mes');
            $table->date('fecha_inicio')->nullable()->after('anio');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['fecha', 'descripcion']);
        });
    }
};
