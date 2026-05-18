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
        $updates = [
            'agents' => 'select vendor_id from users where users.id = agents.user_id',
            'bonus_assignments' => 'select vendor_id from bonuses where bonuses.id = bonus_assignments.bonus_id',
            'raffle_numbers' => 'select vendor_id from raffles where raffles.id = raffle_numbers.raffle_id',
            'line_ratings' => 'select vendor_id from lines where lines.id = line_ratings.line_id',
            'notification_preferences' => 'select vendor_id from agents where agents.id = notification_preferences.agent_id',
            'line_agents' => 'select vendor_id from lines where lines.id = line_agents.line_id',
            'line_agent_permissions' => 'select vendor_id from lines where lines.id = line_agent_permissions.line_id',
            'line_clients' => 'select vendor_id from lines where lines.id = line_clients.line_id',
            'line_platform' => 'select vendor_id from lines where lines.id = line_platform.line_id',
            'line_raffle' => 'select vendor_id from lines where lines.id = line_raffle.line_id',
            'user_notifications' => 'select vendor_id from users where users.id = user_notifications.user_id',
        ];

        foreach ($updates as $tableName => $subquery) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'vendor_id')) {
                DB::statement("update {$tableName} set vendor_id = ({$subquery}) where vendor_id is null");
            }
        }
    }
};
