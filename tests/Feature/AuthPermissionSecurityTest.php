<?php

namespace Tests\Feature;

use App\Livewire\Auth\ClientLogin;
use App\Livewire\Auth\Login;
use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Role;
use App\Models\User;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthPermissionSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_line_switch_requires_authentication(): void
    {
        $line = Line::create(['name' => 'Linea Cerrada', 'status' => 'active']);

        $this->post(route('session.line', $line->id))
            ->assertRedirect(route('login'));
    }

    public function test_stale_agent_session_does_not_loop_login(): void
    {
        $this->withSession(['active_agent_id' => 999, 'active_line_id' => 999])
            ->get(route('admin.login'))
            ->assertOk();

        $this->assertNull(session('active_agent_id'));
        $this->assertNull(session('active_line_id'));
    }

    public function test_agent_cannot_switch_to_unassigned_line(): void
    {
        [$user, $agent, $line] = $this->agentWithLine();
        $otherLine = Line::create(['name' => 'Linea Ajena', 'status' => 'active']);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->post(route('session.line', $otherLine->id))
            ->assertForbidden();
    }

    public function test_agent_without_client_permission_cannot_access_clients(): void
    {
        [$user, $agent, $line] = $this->agentWithLine();

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('clientes'))
            ->assertForbidden();
    }

    public function test_agent_with_client_permission_can_access_clients(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::USER_READ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('clientes'))
            ->assertOk();
    }

    public function test_agent_without_chat_permission_cannot_access_chats(): void
    {
        [$user, $agent, $line] = $this->agentWithLine();

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('chats'))
            ->assertForbidden();
    }

    public function test_agent_with_ticket_permission_can_access_chats(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::TICKET_READ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('chats'))
            ->assertOk();
    }

    public function test_sales_screen_uses_line_edit_permission(): void
    {
        [$user, $agent, $line] = $this->agentWithLine();

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('ventas'))
            ->assertForbidden();

        LineAgentPermission::create([
            'line_id' => $line->id,
            'agent_id' => $agent->id,
            'permission' => Permissions::LINE_EDIT,
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('ventas'))
            ->assertOk();
    }

    public function test_editor_home_requires_dedicated_home_permission(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([
            Permissions::BONO_UPDATE,
            Permissions::LINE_EDIT,
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('editor-home'))
            ->assertForbidden();

        LineAgentPermission::create([
            'line_id' => $line->id,
            'agent_id' => $agent->id,
            'permission' => Permissions::HOME_EDIT,
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('editor-home'))
            ->assertOk();
    }

    public function test_non_admin_cannot_access_platform_manager_even_with_line_permission(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::PLATFORM_READ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('platforms.master'))
            ->assertForbidden();
    }

    public function test_agent_cannot_open_detail_for_unassigned_line(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::LINE_VIEW]);
        $otherLine = Line::create(['name' => 'Linea Ajena', 'status' => 'active']);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('lineas.detail', $otherLine->id))
            ->assertForbidden();
    }

    public function test_inactive_client_cannot_login(): void
    {
        $role = $this->role(Roles::CLIENTE, 'Cliente');
        User::factory()->create([
            'role_id' => $role->id,
            'username' => 'cliente_inactivo',
            'email' => 'cliente-inactivo@test.local',
            'password' => Hash::make('password'),
            'status' => 'inactive',
        ]);

        Livewire::test(ClientLogin::class)
            ->set('username', 'cliente_inactivo')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['username']);

        $this->assertGuest();
    }

    public function test_client_cannot_login_to_panel(): void
    {
        $role = $this->role(Roles::CLIENTE, 'Cliente');
        User::factory()->create([
            'role_id' => $role->id,
            'username' => 'cliente_panel',
            'email' => 'cliente-panel@test.local',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        Livewire::test(Login::class)
            ->set('username', 'cliente_panel')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['username']);

        $this->assertGuest();
    }

    public function test_admin_cannot_login_to_client_frontend(): void
    {
        $role = $this->role(Roles::ADMIN, 'Admin');
        User::factory()->create([
            'role_id' => $role->id,
            'username' => 'admin_cliente',
            'email' => 'admin-cliente@test.local',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        Livewire::test(ClientLogin::class)
            ->set('username', 'admin_cliente')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['username']);

        $this->assertGuest();
    }

    private function agentWithLine(array $permissions = []): array
    {
        $role = $this->role(Roles::AGENTE, 'Agente');
        $user = User::factory()->create([
            'role_id' => $role->id,
            'username' => 'agente_'.uniqid(),
            'status' => 'active',
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'cargo' => 'agente',
            'status' => 'active',
        ]);

        $line = Line::create([
            'name' => 'Linea '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        LineAgent::create([
            'line_id' => $line->id,
            'agent_id' => $agent->id,
            'role' => LineRoles::MIEMBRO,
            'is_active' => true,
        ]);

        foreach ($permissions as $permission) {
            LineAgentPermission::create([
                'line_id' => $line->id,
                'agent_id' => $agent->id,
                'permission' => $permission,
            ]);
        }

        return [$user, $agent, $line];
    }

    private function role(string $name, string $label): Role
    {
        return Role::firstOrCreate(['name' => $name], ['label' => $label]);
    }
}
