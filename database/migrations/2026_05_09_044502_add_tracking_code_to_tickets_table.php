<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('tracking_code', 20)->nullable()->unique()->after('id');
        });

        DB::table('tickets')->whereNull('tracking_code')->orderBy('id')->each(function ($ticket) {
            DB::table('tickets')->where('id', $ticket->id)->update([
                'tracking_code' => 'TKT-' . strtoupper(substr(md5($ticket->id . 'x9z'), 0, 6)),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('tracking_code');
        });
    }
};
