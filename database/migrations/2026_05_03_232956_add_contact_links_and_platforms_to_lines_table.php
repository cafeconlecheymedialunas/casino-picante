<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->json('contact_links')->nullable(); // [{type: 'whatsapp', value: '+54111234...'}, ...]
            $table->json('platforms')->nullable(); // ['ios', 'android', 'web', ...]
        });
    }

    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn(['contact_links', 'platforms']);
        });
    }
};
