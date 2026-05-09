<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE raffles MODIFY COLUMN status ENUM('active','inactive','finished') NOT NULL DEFAULT 'inactive'");
            return;
        }

        // SQLite: rebuild the table replacing the CHECK constraint
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement('
            CREATE TABLE "raffles_new" (
                "id" integer primary key autoincrement not null,
                "line_id" varchar,
                "platform_id" integer,
                "title" varchar not null,
                "description" text,
                "status" varchar check ("status" in (\'active\', \'inactive\', \'finished\')) not null default \'inactive\',
                "start_date" datetime not null,
                "end_date" datetime not null,
                "start_number" integer not null default \'1\',
                "end_number" integer,
                "winner_user_id" integer,
                "winner_number" integer,
                "created_at" datetime,
                "updated_at" datetime,
                "prizes" text,
                "numbers_limit" integer,
                foreign key("platform_id") references "platforms"("id") on delete set null,
                foreign key("winner_user_id") references "users"("id") on delete set null
            )
        ');
        DB::statement('INSERT INTO "raffles_new" SELECT * FROM "raffles"');
        DB::statement('DROP TABLE "raffles"');
        DB::statement('ALTER TABLE "raffles_new" RENAME TO "raffles"');
        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE raffles MODIFY COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'inactive'");
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement('
            CREATE TABLE "raffles_new" (
                "id" integer primary key autoincrement not null,
                "line_id" varchar,
                "platform_id" integer,
                "title" varchar not null,
                "description" text,
                "status" varchar check ("status" in (\'active\', \'inactive\')) not null default \'inactive\',
                "start_date" datetime not null,
                "end_date" datetime not null,
                "start_number" integer not null default \'1\',
                "end_number" integer,
                "winner_user_id" integer,
                "winner_number" integer,
                "created_at" datetime,
                "updated_at" datetime,
                "prizes" text,
                "numbers_limit" integer,
                foreign key("platform_id") references "platforms"("id") on delete set null,
                foreign key("winner_user_id") references "users"("id") on delete set null
            )
        ');
        DB::statement('INSERT INTO "raffles_new" SELECT * FROM "raffles"');
        DB::statement('DROP TABLE "raffles"');
        DB::statement('ALTER TABLE "raffles_new" RENAME TO "raffles"');
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
