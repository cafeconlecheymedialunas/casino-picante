<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Platform;
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

        $admin = User::updateOrCreate(
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

        $platform = Platform::updateOrCreate(
            ['name' => 'Platino'],
            [
                'slug' => 'platino',
                'is_active' => true,
                'description' => 'Plataforma principal',
            ]
        );

        $line = Line::updateOrCreate(
            ['name' => 'Línea Dorada'],
            [
                'description' => 'Línea principal de juego',
                'status' => 'active',
            ]
        );

        $line->platforms()->syncWithoutDetaching([
            $platform->id => ['is_active' => true],
        ]);

        $agent = Agent::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'username' => 'admin_agent',
                'name' => 'Admin',
                'apellido' => 'Agente',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        LineAgent::updateOrCreate(
            ['line_id' => $line->id, 'agent_id' => $agent->id],
            [
                'role' => 'encargado',
                'is_active' => true,
                'porcentaje_ganancia' => 30,
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
