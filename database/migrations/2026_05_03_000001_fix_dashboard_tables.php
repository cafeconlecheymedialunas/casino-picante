<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix agent_permissions: rename 'permission' to 'level' only if column exists
        try {
            Schema::table('agent_permissions', function (Blueprint $table) {
                if (Schema::hasColumn('agent_permissions', 'permission')) {
                    $table->renameColumn('permission', 'level');
                }
            });
        } catch (Exception $e) {
            // Column might already be named 'level'
        }

        // Fix promotions: add missing columns
        Schema::table('promotions', function (Blueprint $table) {
            if (! Schema::hasColumn('promotions', 'type')) {
                $table->string('type')->default('bonus')->after('code');
            }
            if (! Schema::hasColumn('promotions', 'bonus_percent')) {
                $table->decimal('bonus_percent', 8, 2)->default(0)->after('type');
            }
            if (! Schema::hasColumn('promotions', 'bonus_amount')) {
                $table->decimal('bonus_amount', 12, 2)->default(0)->after('bonus_percent');
            }
            if (! Schema::hasColumn('promotions', 'min_deposit')) {
                $table->decimal('min_deposit', 12, 2)->default(0)->after('bonus_amount');
            }
            if (! Schema::hasColumn('promotions', 'max_bonus')) {
                $table->decimal('max_bonus', 12, 2)->default(0)->after('min_deposit');
            }
            if (! Schema::hasColumn('promotions', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('max_bonus');
            }
            if (! Schema::hasColumn('promotions', 'recurring_days')) {
                $table->json('recurring_days')->nullable()->after('is_recurring');
            }
        });

        // Fix lines: add 'type' and 'phone' columns
        Schema::table('lines', function (Blueprint $table) {
            if (! Schema::hasColumn('lines', 'type')) {
                $table->string('type')->default('whatsapp')->after('name');
            }
            if (! Schema::hasColumn('lines', 'phone')) {
                $table->string('phone')->nullable()->after('type');
            }
        });

    }

    public function down(): void
    {
        Schema::table('agent_permissions', function (Blueprint $table) {
            $table->renameColumn('level', 'permission');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['type', 'bonus_percent', 'bonus_amount', 'min_deposit', 'max_bonus', 'is_recurring', 'recurring_days']);
        });

        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn(['type', 'phone']);
        });
    }
};
