<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->string('portada_url')->nullable()->after('best_sales');
            $table->string('perfil_url')->nullable()->after('portada_url');
        });

        Schema::table('line_agents', function (Blueprint $table) {
            $table->decimal('porcentaje_ganancia', 5, 2)->nullable()->default(0)->after('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn(['portada_url', 'perfil_url']);
        });

        Schema::table('line_agents', function (Blueprint $table) {
            $table->dropColumn('porcentaje_ganancia');
        });
    }
};
