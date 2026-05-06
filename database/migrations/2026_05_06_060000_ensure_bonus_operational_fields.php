<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {
            if (! Schema::hasColumn('bonuses', 'code')) {
                $table->string('code')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('bonuses', 'total_quantity')) {
                $table->integer('total_quantity')->nullable()->after('max_bonus');
            }

            if (! Schema::hasColumn('bonuses', 'per_user_limit')) {
                $table->integer('per_user_limit')->nullable()->after('total_quantity');
            }
        });

        Schema::table('bonus_assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('bonus_assignments', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bonus_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('bonus_assignments', 'assigned_at')) {
                $table->dropColumn('assigned_at');
            }
        });

        Schema::table('bonuses', function (Blueprint $table) {
            if (Schema::hasColumn('bonuses', 'per_user_limit')) {
                $table->dropColumn('per_user_limit');
            }

            if (Schema::hasColumn('bonuses', 'total_quantity')) {
                $table->dropColumn('total_quantity');
            }

            if (Schema::hasColumn('bonuses', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });
    }
};
