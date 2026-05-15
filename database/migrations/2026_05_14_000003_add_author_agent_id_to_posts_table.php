<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'author_agent_id')) {
                $table->foreignId('author_agent_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('agents')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'author_agent_id')) {
                $table->dropConstrainedForeignId('author_agent_id');
            }
        });
    }
};
