<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {
            if (! Schema::hasColumn('bonuses', 'bonus_percent')) {
                $table->decimal('bonus_percent', 8, 2)->default(0)->after('type');
            }
            if (! Schema::hasColumn('bonuses', 'bonus_amount')) {
                $table->decimal('bonus_amount', 12, 2)->default(0)->after('bonus_percent');
            }
            if (! Schema::hasColumn('bonuses', 'min_deposit')) {
                $table->decimal('min_deposit', 12, 2)->default(0)->after('bonus_amount');
            }
            if (! Schema::hasColumn('bonuses', 'max_bonus')) {
                $table->decimal('max_bonus', 12, 2)->default(0)->after('min_deposit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {
            $table->dropColumn(['bonus_percent', 'bonus_amount', 'min_deposit', 'max_bonus']);
        });
    }
};
