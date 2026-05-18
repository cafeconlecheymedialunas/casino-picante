<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('home_config') && ! Schema::hasColumn('home_config', 'vendor_id')) {
            Schema::table('home_config', function (Blueprint $table): void {
                $table->foreignId('vendor_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('home_config') && Schema::hasColumn('home_config', 'vendor_id')) {
            Schema::table('home_config', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('vendor_id');
            });
        }
    }
};
