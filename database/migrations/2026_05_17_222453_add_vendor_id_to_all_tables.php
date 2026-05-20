<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tablas que necesitan vendor_id para aislamiento total.
     */
    protected array $tables = [
        'users',
        'platforms',
        'bonuses',
        'raffles',
        'posts',
        'sales',
        'chats',
        'chat_messages',
        'tickets',
        'ticket_messages',
        'home_config',
        'home_sections',
        'carousel_items',
        'comments',
        'dashboard_notifications',
        'categories',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'vendor_id')) {
                        $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('cascade');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'vendor_id')) {
                        $table->dropConstrainedForeignId('vendor_id');
                    }
                });
            }
        }
    }
};
