<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dashboard_notifications', function (Blueprint $table) {
            $table->string('module', 50)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('dashboard_notifications', function (Blueprint $table) {
            $table->dropColumn('module');
        });
    }
};
