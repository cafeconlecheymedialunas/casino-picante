<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Support\Roles;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [Roles::ADMIN => 'Admin'],
            [Roles::AGENTE => 'Agente'],
            [Roles::CLIENTE => 'Cliente'],
        ];

        foreach ($roles as $roleData) {
            foreach ($roleData as $name => $label) {
                Role::updateOrCreate(['name' => $name], ['label' => $label]);
            }
        }

        $this->command->info('Roles sembrados exitosamente.');
    }
}
