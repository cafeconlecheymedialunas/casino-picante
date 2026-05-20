<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vendors')) {
            return;
        }

        if (Schema::hasColumn('vendors', 'social_links') && Schema::hasColumn('vendors', 'contacts')) {
            DB::table('vendors')
                ->whereNull('contacts')
                ->whereNotNull('social_links')
                ->update(['contacts' => DB::raw('social_links')]);
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'vendor_id')) {
            DB::table('vendors')
                ->select(['id', 'user_id'])
                ->whereNotNull('user_id')
                ->orderBy('id')
                ->each(function ($vendor): void {
                    DB::table('users')
                        ->where('id', $vendor->user_id)
                        ->update(['vendor_id' => $vendor->id]);
                });
        }

        $duplicateUserIds = DB::table('vendors')
            ->select('user_id')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if (! $duplicateUserIds && ! Schema::hasIndex('vendors', 'vendors_user_id_unique')) {
            Schema::table('vendors', function (Blueprint $table): void {
                $table->unique('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vendors') && Schema::hasIndex('vendors', 'vendors_user_id_unique')) {
            Schema::table('vendors', function (Blueprint $table): void {
                $table->dropUnique('vendors_user_id_unique');
            });
        }
    }
};
