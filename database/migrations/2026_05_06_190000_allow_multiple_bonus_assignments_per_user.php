<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonus_assignments', function (Blueprint $table) {
            $table->dropUnique('bonus_assignments_bonus_id_user_id_unique');
            $table->index(['bonus_id', 'user_id'], 'bonus_assignments_bonus_user_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bonus_assignments', function (Blueprint $table) {
            $table->dropIndex('bonus_assignments_bonus_user_idx');
            $table->unique(['bonus_id', 'user_id']);
        });
    }
};
