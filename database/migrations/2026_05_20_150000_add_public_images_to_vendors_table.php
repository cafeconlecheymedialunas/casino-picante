<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table): void {
            if (! Schema::hasColumn('vendors', 'hero_image')) {
                $table->string('hero_image')->nullable()->after('logo');
            }

            if (! Schema::hasColumn('vendors', 'portrait_image')) {
                $table->string('portrait_image')->nullable()->after('hero_image');
            }
        });

        DB::table('vendors')
            ->whereNull('hero_image')
            ->update([
                'hero_image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=1800&auto=format&fit=crop',
            ]);

        DB::table('vendors')
            ->whereNull('portrait_image')
            ->update([
                'portrait_image' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=900&auto=format&fit=crop',
            ]);
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table): void {
            if (Schema::hasColumn('vendors', 'portrait_image')) {
                $table->dropColumn('portrait_image');
            }

            if (Schema::hasColumn('vendors', 'hero_image')) {
                $table->dropColumn('hero_image');
            }
        });
    }
};
