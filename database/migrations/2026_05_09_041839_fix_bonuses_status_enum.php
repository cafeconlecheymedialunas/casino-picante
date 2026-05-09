<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update legacy 'paused' rows before changing the enum.
        DB::table('bonuses')->where('status', 'paused')->update(['status' => 'active']);

        Schema::table('bonuses', function (Blueprint $table) {
            $table->enum('status', ['active', 'upcoming', 'expired'])->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {
            $table->enum('status', ['active', 'paused'])->default('active')->change();
        });
    }
};
