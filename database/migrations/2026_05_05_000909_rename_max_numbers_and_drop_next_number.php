<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            // Rename max_numbers to end_number
            if (Schema::hasColumn('raffles', 'max_numbers')) {
                $table->renameColumn('max_numbers', 'end_number');
            }
            
            // Drop next_number
            if (Schema::hasColumn('raffles', 'next_number')) {
                $table->dropColumn('next_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->renameColumn('end_number', 'max_numbers');
            $table->unsignedInteger('next_number')->default(1)->after('start_number');
        });
    }
};
