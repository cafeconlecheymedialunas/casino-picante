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
        Schema::table('agents', function (Blueprint $table) {
            if (! Schema::hasColumn('agents', 'username')) {
                $table->string('username')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('agents', 'apellido')) {
                $table->string('apellido')->nullable()->after('name');
            }

            if (! Schema::hasColumn('agents', 'cargo')) {
                $table->string('cargo')->default('agente')->after('parent_id');
            }
        });

        DB::table('agents')
            ->whereNull('username')
            ->orWhere('username', '')
            ->orderBy('id')
            ->get(['id', 'name', 'email'])
            ->each(function ($agent): void {
                $base = Str::slug($agent->name, '_') ?: Str::before($agent->email, '@') ?: 'agente';
                $base = Str::limit($base, 50, '');
                $username = $base;
                $suffix = 1;

                while (DB::table('agents')->where('username', $username)->where('id', '!=', $agent->id)->exists()) {
                    $username = Str::limit($base, 46, '').'_'.$suffix++;
                }

                DB::table('agents')->where('id', $agent->id)->update(['username' => $username]);
            });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            if (Schema::hasColumn('agents', 'username')) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            }

            if (Schema::hasColumn('agents', 'apellido')) {
                $table->dropColumn('apellido');
            }

            if (Schema::hasColumn('agents', 'cargo')) {
                $table->dropColumn('cargo');
            }
        });
    }
};
