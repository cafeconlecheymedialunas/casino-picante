<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Support\Roles;
use Illuminate\Database\Seeder;

class DirectVendorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', Roles::ADMIN))->first()
            ?? User::first();

        Vendor::firstOrCreate(
            ['slug' => 'general'],
            [
                'user_id'     => $admin?->id,
                'name'        => 'General',
                'description' => 'Líneas directas administradas por la red. Sin cajero intermediario.',
                'is_active'   => true,
                'is_direct'   => true,
                'contacts'    => [],
                'features'    => [],
            ]
        );
    }
}
