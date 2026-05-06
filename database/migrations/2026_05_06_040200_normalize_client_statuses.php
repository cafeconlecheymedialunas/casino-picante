<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereIn('status', ['pending', 'blocked'])->update(['status' => 'inactive']);
    }

    public function down(): void
    {
        //
    }
};
