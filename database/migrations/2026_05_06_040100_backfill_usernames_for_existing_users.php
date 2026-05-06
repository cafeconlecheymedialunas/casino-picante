<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNull('username')
            ->orWhere('username', '')
            ->orderBy('id')
            ->get(['id', 'name', 'email'])
            ->each(function ($user): void {
                $base = Str::slug($user->name, '_') ?: Str::before($user->email, '@') ?: 'cliente';
                $base = Str::limit($base, 50, '');
                $username = $base;
                $suffix = 1;

                while (DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                    $username = Str::limit($base, 46, '').'_'.$suffix++;
                }

                DB::table('users')->where('id', $user->id)->update(['username' => $username]);
            });
    }

    public function down(): void
    {
        DB::table('users')->update(['username' => null]);
    }
};
