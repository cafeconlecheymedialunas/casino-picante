<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Platform;
use App\Models\Role;
use App\Models\User;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = $this->seedRoles();

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role_id' => $roles[Roles::ADMIN]->id,
                'username' => 'admin',
                'name' => 'Admin',
                'apellido' => 'General',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $line = Line::updateOrCreate(
            ['name' => 'Linea Principal'],
            [
                'type' => 'whatsapp',
                'phone' => '+54 9 11 0000 0000',
                'icon' => 'RP',
                'description' => 'Linea operativa principal',
                'status' => 'active',
                'permissions' => Permissions::all(),
            ]
        );

        Line::updateOrCreate(
            ['name' => 'Linea VIP'],
            [
                'type' => 'whatsapp',
                'phone' => '+54 9 11 0000 0001',
                'icon' => 'VIP',
                'description' => 'Linea para clientes VIP',
                'status' => 'active',
                'permissions' => Permissions::all(),
            ]
        );

        Platform::updateOrCreate(
            ['slug' => 'casino-central'],
            [
                'name' => 'Casino Central',
                'website_url' => 'https://example.com',
                'is_active' => true,
            ]
        );

        $encargadoUser = $this->seedUser($roles[Roles::AGENTE]->id, 'encargado_demo', 'Encargado', 'Demo', 'encargado@example.com');
        $agenteUser = $this->seedUser($roles[Roles::AGENTE]->id, 'agente_demo', 'Agente', 'Demo', 'agente@example.com');
        $clienteUser = $this->seedUser($roles[Roles::CLIENTE]->id, 'cliente_demo', 'Cliente', 'Demo', 'cliente@example.com');

        $encargado = $this->seedAgent($encargadoUser, 'super_agente');
        $agente = $this->seedAgent($agenteUser, 'agente');

        $line->update(['encargado_id' => $encargado->id]);

        $encargadoPivot = LineAgent::updateOrCreate(
            ['line_id' => $line->id, 'agent_id' => $encargado->id],
            ['role' => LineRoles::ENCARGADO, 'is_active' => true]
        );

        $agentePivot = LineAgent::updateOrCreate(
            ['line_id' => $line->id, 'agent_id' => $agente->id],
            ['role' => LineRoles::MIEMBRO, 'is_active' => true, 'parent_id' => $encargado->id]
        );

        $encargadoPivot->syncPermissions(Permissions::all());
        $agentePivot->syncPermissions([
            Permissions::LINE_READ,
            Permissions::USER_READ,
            Permissions::TICKET_READ,
            Permissions::TICKET_UPDATE,
            Permissions::TICKET_CLOSE,
            Permissions::BONO_READ,
            Permissions::SORTEO_READ,
        ]);

        $line->clients()->syncWithoutDetaching([$clienteUser->id => ['is_active' => true]]);

        $this->command->info('Seed limpio creado. Admin: admin@example.com / password');
        $this->command->info('Agente encargado: encargado@example.com / password');
        $this->command->info('Cliente: cliente@example.com / password');
    }

    private function seedRoles(): array
    {
        $roles = [];
        foreach ([
            Roles::ADMIN => 'Admin',
            Roles::AGENTE => 'Agente',
            Roles::CLIENTE => 'Cliente',
        ] as $name => $label) {
            $roles[$name] = Role::updateOrCreate(['name' => $name], ['label' => $label]);
        }

        return $roles;
    }

    private function seedUser(int $roleId, string $username, string $name, string $apellido, string $email): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'role_id' => $roleId,
                'username' => $username,
                'name' => $name,
                'apellido' => $apellido,
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
    }

    private function seedAgent(User $user, string $cargo): Agent
    {
        return Agent::updateOrCreate(
            ['user_id' => $user->id],
            [
                'username' => $user->username,
                'name' => $user->name,
                'apellido' => $user->apellido,
                'email' => $user->email,
                'password' => $user->password,
                'cargo' => $cargo,
                'status' => 'active',
            ]
        );
    }
}
