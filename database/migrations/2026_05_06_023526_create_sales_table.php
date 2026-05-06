<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_id')->constrained()->onDelete('cascade');
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->integer('mes');
            $table->integer('anio');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('monto_fichas', 12, 2)->default(0);
            $table->decimal('ganancia_superagente', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['line_id', 'platform_id', 'mes', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
