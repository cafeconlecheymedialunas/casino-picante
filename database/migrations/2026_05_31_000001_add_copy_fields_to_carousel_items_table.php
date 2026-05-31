<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carousel_items', function (Blueprint $table): void {
            $table->string('description', 500)->nullable()->after('title');
            $table->string('cta_text')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('carousel_items', function (Blueprint $table): void {
            $table->dropColumn(['description', 'cta_text']);
        });
    }
};
