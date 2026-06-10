<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonus_assignments', function (Blueprint $table) {
            $table->dropForeign(['bonus_id']);
            $table->dropForeign(['user_id']);
            $table->dropUnique('bonus_assignments_bonus_id_user_id_unique');
            $table->index(['bonus_id', 'user_id'], 'bonus_assignments_bonus_user_idx');
            $table->foreign('bonus_id')->references('id')->on('bonuses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('bonus_assignments', function (Blueprint $table) {
            $table->dropForeign(['bonus_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex('bonus_assignments_bonus_user_idx');
            $table->unique(['bonus_id', 'user_id']);
            $table->foreign('bonus_id')->references('id')->on('bonuses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
