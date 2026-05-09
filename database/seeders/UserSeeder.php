<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure at least one admin user exists. If none, create a default admin.
        $adminRole = Role::where('name', Roles::ADMIN)->first();

        if ($adminRole) {
            $adminCount = User::where('role_id', $adminRole->id)->count();
            if ($adminCount === 0) {
                User::create([
                    'email' => 'admin@example.com',
                    'username' => 'admin',
                    'name' => 'Admin',
                    'apellido' => 'System',
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'role_id' => $adminRole->id,
                ]);
                $this->command->info('Admin por defecto creado: admin@example.com / password');
            } else {
                $this->command->info('Admin existente detectado, no se creó uno nuevo.');
            }
        } else {
            $this->command->error('Role ADMIN no encontrado. Ejecute RoleSeeder primero.');
        }
    }
}
