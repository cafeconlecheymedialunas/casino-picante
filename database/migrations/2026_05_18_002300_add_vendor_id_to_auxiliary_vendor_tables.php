<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'agents',
        'bonus_assignments',
        'raffle_numbers',
        'line_ratings',
        'notification_preferences',
        'line_agents',
        'line_agent_permissions',
        'line_clients',
        'line_platform',
        'line_raffle',
        'user_notifications',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'vendor_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                    $after = Schema::hasColumn($tableName, 'id') ? 'id' : null;
                    $column = $table->foreignId('vendor_id')->nullable()->constrained()->cascadeOnDelete();

                    if ($after) {
                        $column->after($after);
                    }
                });
            }
        }

        $this->backfill();
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'vendor_id')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropConstrainedForeignId('vendor_id');
                });
            }
        }
    }

    private function backfill(): void
    {
        $joins = [
            'agents'                  => ['users',   'users.id = agents.user_id'],
            'bonus_assignments'       => ['bonuses',  'bonuses.id = bonus_assignments.bonus_id'],
            'raffle_numbers'          => ['raffles',  'raffles.id = raffle_numbers.raffle_id'],
            'line_ratings'            => ['lines',    'lines.id = line_ratings.line_id'],
            'notification_preferences'=> ['agents',   'agents.id = notification_preferences.agent_id'],
            'line_agents'             => ['lines',    'lines.id = line_agents.line_id'],
            'line_agent_permissions'  => ['lines',    'lines.id = line_agent_permissions.line_id'],
            'line_clients'            => ['lines',    'lines.id = line_clients.line_id'],
            'line_platform'           => ['lines',    'lines.id = line_platform.line_id'],
            'line_raffle'             => ['lines',    'lines.id = line_raffle.line_id'],
            'user_notifications'      => ['users',    'users.id = user_notifications.user_id'],
        ];

        foreach ($joins as $tableName => [$joinTable, $joinCondition]) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'vendor_id')) {
                DB::statement("UPDATE `{$tableName}` INNER JOIN `{$joinTable}` ON {$joinCondition} SET `{$tableName}`.vendor_id = `{$joinTable}`.vendor_id WHERE `{$tableName}`.vendor_id IS NULL");
            }
        }
    }
};
