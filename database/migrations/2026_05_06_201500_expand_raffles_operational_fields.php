<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            if (! Schema::hasColumn('raffles', 'prizes')) {
                $table->json('prizes')->nullable()->after('description');
            }

            if (! Schema::hasColumn('raffles', 'numbers_limit')) {
                $table->unsignedInteger('numbers_limit')->nullable()->after('end_number');
            }
        });

        Schema::create('line_raffle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_id')->constrained('lines')->cascadeOnDelete();
            $table->foreignId('raffle_id')->constrained('raffles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['line_id', 'raffle_id']);
        });

        Schema::table('raffle_numbers', function (Blueprint $table) {
            if (! Schema::hasColumn('raffle_numbers', 'line_id')) {
                $table->foreignId('line_id')->nullable()->after('user_id')->constrained('lines')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('raffle_numbers', function (Blueprint $table) {
            if (Schema::hasColumn('raffle_numbers', 'line_id')) {
                $table->dropConstrainedForeignId('line_id');
            }
        });

        Schema::dropIfExists('line_raffle');

        Schema::table('raffles', function (Blueprint $table) {
            if (Schema::hasColumn('raffles', 'numbers_limit')) {
                $table->dropColumn('numbers_limit');
            }

            if (Schema::hasColumn('raffles', 'prizes')) {
                $table->dropColumn('prizes');
            }
        });
    }
};
