<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'wants_bonus_emails')) {
                $table->boolean('wants_bonus_emails')->default(false)->after('contact');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'wants_bonus_emails')) {
                $table->dropColumn('wants_bonus_emails');
            }
        });
    }
};
