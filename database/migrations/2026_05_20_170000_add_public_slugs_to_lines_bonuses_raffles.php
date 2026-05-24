<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            if (! Schema::hasColumn('lines', 'slug')) {
                $table->string('slug')->nullable()->after('name')->index();
            }
        });

        Schema::table('bonuses', function (Blueprint $table) {
            if (! Schema::hasColumn('bonuses', 'slug')) {
                $table->string('slug')->nullable()->after('title')->index();
            }
        });

        Schema::table('raffles', function (Blueprint $table) {
            if (! Schema::hasColumn('raffles', 'slug')) {
                $table->string('slug')->nullable()->after('title')->index();
            }
        });

        $this->backfill('lines', 'name');
        $this->backfill('bonuses', 'title');
        $this->backfill('raffles', 'title');
    }

    public function down(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            if (Schema::hasColumn('raffles', 'slug')) {
                $table->dropColumn('slug');
            }
        });

        Schema::table('bonuses', function (Blueprint $table) {
            if (Schema::hasColumn('bonuses', 'slug')) {
                $table->dropColumn('slug');
            }
        });

        Schema::table('lines', function (Blueprint $table) {
            if (Schema::hasColumn('lines', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }

    private function backfill(string $table, string $sourceColumn): void
    {
        $used = [];

        DB::table($table)
            ->select(['id', $sourceColumn, 'slug'])
            ->orderBy('id')
            ->get()
            ->each(function ($row) use ($table, $sourceColumn, &$used) {
                $base = Str::slug((string) ($row->{$sourceColumn} ?? '')) ?: $table.'-'.$row->id;
                $slug = $base;
                $suffix = 2;

                while (isset($used[$slug])) {
                    $slug = $base.'-'.$suffix;
                    $suffix++;
                }

                $used[$slug] = true;

                if ($row->slug !== $slug) {
                    DB::table($table)->where('id', $row->id)->update(['slug' => $slug]);
                }
            });
    }
};
