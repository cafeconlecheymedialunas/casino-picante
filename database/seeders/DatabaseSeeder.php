<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role_id' => Role::where('name', Roles::ADMIN)->first()?->id,
                'username' => 'admin',
                'name' => 'Admin',
                'apellido' => 'General',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $this->command->info('Seed creado. Admin: admin@example.com / password');
    }

    private function seedRoles(): void
    {
        foreach ([
            Roles::ADMIN => 'Admin',
            Roles::AGENTE => 'Agente',
            Roles::CLIENTE => 'Cliente',
        ] as $name => $label) {
            Role::updateOrCreate(['name' => $name], ['label' => $label]);
        }
    }
}
