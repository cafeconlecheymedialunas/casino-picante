<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('id');
            $table->integer('total_quantity')->nullable()->after('max_bonus');
            $table->integer('per_user_limit')->nullable()->after('total_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {
            $table->dropColumn(['code', 'total_quantity', 'per_user_limit']);
        });
    }
};
