<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedIfEmpty extends Command
{
    protected $signature = 'db:seed-if-empty';
    protected $description = 'Run seeder only if the database has no users';

    public function handle(): void
    {
        $count = DB::table('users')->count();

        if ($count > 0) {
            $this->info('Database already has data. Skipping seed.');
            return;
        }

        $this->info('Empty database detected. Running seeder...');
        $this->call('db:seed', ['--force' => true, '--class' => 'DatabaseSeeder']);
        $this->info('Seed complete.');
    }
}
