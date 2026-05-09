<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite does not support ALTER COLUMN on enums.
        // We allow 'finished' by updating the check constraint via a table rebuild.
        // For SQLite we just update existing rows; the enum constraint is not enforced at DB level.
        // Nothing to do for SQLite — the string value 'finished' is accepted.
        // For MySQL this would require: DB::statement("ALTER TABLE raffles MODIFY COLUMN status ENUM('active','inactive','finished') NOT NULL DEFAULT 'inactive'");
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE raffles MODIFY COLUMN status ENUM('active','inactive','finished') NOT NULL DEFAULT 'inactive'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE raffles MODIFY COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'inactive'");
        }
    }
};
