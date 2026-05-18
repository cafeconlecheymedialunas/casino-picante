<?php

namespace Tests\Feature;

use App\Livewire\Auth\ClientLogin;
use App\Livewire\Auth\Login;
use App\Livewire\Agentes;
use App\Livewire\Settings;
use App\Livewire\Users\UsersIndex;
use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Raffle;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
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

    public function test_cajero_can_access_full_vendor_panel(): void
    {
        [$user, $vendor] = $this->cajeroVendor();

        Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Vendor '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        foreach ([
            'dashboard',
            'clientes',
            'agentes',
            'lineas',
            'platforms.master',
            'editor-home',
            'novedades',
            'bonos',
            'ventas',
            'sorteos',
            'tickets',
            'settings',
        ] as $route) {
            $this->actingAs($user)
                ->withSession(['active_vendor_id' => $vendor->id])
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_cajero_cannot_access_global_vendor_manager(): void
    {
        [$user, $vendor] = $this->cajeroVendor();

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->get(route('admin.vendors'))
            ->assertForbidden();
    }

    public function test_cajero_with_inactive_vendor_cannot_fall_back_to_global_scope(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $vendor->update(['is_active' => false]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_cajero_can_switch_between_own_vendor_lines_and_all_lines(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Propia '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->post(route('session.line', $line->id))
            ->assertRedirect();

        $this->assertSame($line->id, session('active_line_id'));

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id, 'active_line_id' => $line->id])
            ->post(route('session.line', 0))
            ->assertRedirect();

        $this->assertNull(session('active_line_id'));
    }

    public function test_cajero_cannot_switch_to_another_vendor_line(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();

        $otherLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Ajena '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->post(route('session.line', $otherLine->id))
            ->assertNotFound();
    }

    public function test_cajero_cannot_switch_to_inactive_line(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Inactiva '.uniqid(),
            'status' => 'inactive',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->post(route('session.line', $line->id))
            ->assertNotFound();
    }

    public function test_cajero_line_scoped_create_requires_explicit_active_line(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Explicit '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id, 'active_line_id' => null]);

        $resolver = new class
        {
            use \App\Traits\HasLinePermissions;
        };

        $this->assertNull($resolver->lineIdForScopedCreate());

        session(['active_line_id' => $line->id]);

        $this->assertSame($line->id, $resolver->lineIdForScopedCreate());
    }

    public function test_cajero_all_lines_view_does_not_show_other_vendor_tickets_or_raffles(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();

        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Ticket Own '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);
        $otherLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Ticket Other '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);
        $client = User::factory()->create(['vendor_id' => $vendor->id, 'status' => 'active']);
        $otherClient = User::factory()->create(['vendor_id' => $otherVendor->id, 'status' => 'active']);

        Ticket::create([
            'vendor_id' => $vendor->id,
            'user_id' => $client->id,
            'line_id' => $line->id,
            'subject' => 'Ticket Vendor Propio',
            'status' => 'open',
            'priority' => 'medium',
        ]);
        Ticket::create([
            'vendor_id' => $otherVendor->id,
            'user_id' => $otherClient->id,
            'line_id' => $otherLine->id,
            'subject' => 'Ticket Vendor Ajeno',
            'status' => 'open',
            'priority' => 'medium',
        ]);

        $raffle = Raffle::withoutGlobalScopes()->create([
            'vendor_id' => $vendor->id,
            'title' => 'Sorteo Vendor Propio',
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'start_number' => 1,
            'end_number' => 10,
            'numbers_limit' => 10,
            'line_id' => $line->id,
        ]);
        $raffle->lines()->sync([$line->id => ['vendor_id' => $vendor->id]]);
        $otherRaffle = Raffle::withoutGlobalScopes()->create([
            'vendor_id' => $otherVendor->id,
            'title' => 'Sorteo Vendor Ajeno',
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'start_number' => 1,
            'end_number' => 10,
            'numbers_limit' => 10,
            'line_id' => $otherLine->id,
        ]);
        $otherRaffle->lines()->sync([$otherLine->id => ['vendor_id' => $otherVendor->id]]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id, 'active_line_id' => null]);

        Livewire::test(\App\Livewire\Tickets::class)
            ->assertSee('Ticket Vendor Propio')
            ->assertDontSee('Ticket Vendor Ajeno');

        Livewire::test(\App\Livewire\Sorteos::class)
            ->assertSee('Sorteo Vendor Propio')
            ->assertDontSee('Sorteo Vendor Ajeno');
    }

    public function test_each_vendor_can_have_independent_home_sections(): void
    {
        [$firstUser, $firstVendor] = $this->cajeroVendor();
        [$secondUser, $secondVendor] = $this->cajeroVendor();

        Line::create([
            'vendor_id' => $firstVendor->id,
            'name' => 'Linea First '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);
        Line::create([
            'vendor_id' => $secondVendor->id,
            'name' => 'Linea Second '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($firstUser)
            ->withSession(['active_vendor_id' => $firstVendor->id])
            ->get(route('editor-home'))
            ->assertOk();

        $this->actingAs($secondUser)
            ->withSession(['active_vendor_id' => $secondVendor->id])
            ->get(route('editor-home'))
            ->assertOk();

        $this->assertDatabaseHas('home_sections', [
            'vendor_id' => $firstVendor->id,
            'section_key' => 'como-empezar',
        ]);
        $this->assertDatabaseHas('home_sections', [
            'vendor_id' => $secondVendor->id,
            'section_key' => 'como-empezar',
        ]);
    }

    public function test_cajero_cannot_assign_client_to_other_vendor_line_by_tampering(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();

        $otherLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Cliente Ajena '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(UsersIndex::class)
            ->set('name', 'Cliente Tamper')
            ->set('email', 'cliente-tamper@example.test')
            ->set('password', 'secret123')
            ->set('avatar', 'avatar_adventurer__red-picantes-01')
            ->set('preferredLineId', $otherLine->id)
            ->set('selectedLines', [$otherLine->id])
            ->call('saveUser')
            ->assertForbidden();
    }

    public function test_cajero_cannot_assign_agent_to_other_vendor_line_by_tampering(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();

        $otherLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Agente Ajena '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Agentes::class)
            ->set('name', 'Agente Tamper')
            ->set('email', 'agente-tamper@example.test')
            ->set('password', 'secret123')
            ->set('avatar', 'avatar_adventurer__red-picantes-01')
            ->set('cargo', 'agente')
            ->set('lineIds', [$otherLine->id])
            ->call('saveAgent')
            ->assertForbidden();
    }

    public function test_cajero_can_edit_own_vendor_settings(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Settings::class)
            ->set('name', 'Cajero Actualizado')
            ->set('slug', 'cajero-actualizado')
            ->set('description', 'Descripcion publica del cajero')
            ->set('contacts', [
                ['type' => 'whatsapp', 'value' => '+5491111111111', 'name' => 'Atencion'],
            ])
            ->set('brandingJson', '{"primary":"#ff6a1a"}')
            ->call('saveVendor')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'name' => 'Cajero Actualizado',
            'slug' => 'cajero-actualizado',
            'description' => 'Descripcion publica del cajero',
        ]);

        $this->assertDatabaseHas('vendors', [
            'id' => $otherVendor->id,
            'name' => $otherVendor->name,
        ]);
    }

    public function test_cajero_can_edit_own_user_settings(): void
    {
        [$user, $vendor] = $this->cajeroVendor();

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Settings::class)
            ->set('cajeroUsername', 'cajero_settings')
            ->set('cajeroName', 'Nombre Cajero')
            ->set('cajeroApellido', 'Apellido Cajero')
            ->set('cajeroEmail', 'cajero-settings@example.test')
            ->set('cajeroPhone', '+5491122222222')
            ->set('cajeroContact', '@cajero')
            ->set('cajeroPassword', 'password')
            ->call('saveCajeroUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'vendor_id' => $vendor->id,
            'username' => 'cajero_settings',
            'name' => 'Nombre Cajero',
            'apellido' => 'Apellido Cajero',
            'email' => 'cajero-settings@example.test',
            'phone' => '+5491122222222',
            'contact' => '@cajero',
        ]);
    }

    public function test_agent_cannot_open_detail_for_unassigned_line(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::LINE_READ]);
        $otherLine = Line::create(['name' => 'Linea Ajena', 'status' => 'active']);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('lineas.detail', $otherLine->id))
            ->assertForbidden();
    }

    public function test_agent_can_access_dashboard_when_permission_exists_on_another_assigned_line(): void
    {
        [$user, $agent, $lineWithoutDashboard] = $this->agentWithLine();
        $lineWithDashboard = Line::create([
            'name' => 'Linea Dashboard '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        LineAgent::create([
            'line_id' => $lineWithDashboard->id,
            'agent_id' => $agent->id,
            'role' => LineRoles::MIEMBRO,
            'is_active' => true,
        ]);

        LineAgentPermission::create([
            'line_id' => $lineWithDashboard->id,
            'agent_id' => $agent->id,
            'permission' => Permissions::DASHBOARD_READ,
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $lineWithoutDashboard->id])
            ->get(route('dashboard'))
            ->assertOk();

        $this->assertSame($lineWithDashboard->id, session('active_line_id'));
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

    private function cajeroVendor(): array
    {
        $role = $this->role(Roles::CAJERO, 'Cajero');
        $user = User::factory()->create([
            'role_id' => $role->id,
            'username' => 'cajero_'.uniqid(),
            'status' => 'active',
        ]);

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => 'Vendor '.uniqid(),
            'slug' => 'vendor-'.uniqid(),
            'is_active' => true,
        ]);

        $user->forceFill(['vendor_id' => $vendor->id])->save();

        return [$user->fresh('role'), $vendor];
    }

    private function role(string $name, string $label): Role
    {
        return Role::firstOrCreate(['name' => $name], ['label' => $label]);
    }
}
