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

        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn(['type', 'phone']);
        });
    }
};
