<?php

namespace Tests\Feature;

use App\Livewire\Agentes;
use App\Livewire\Auth\ClientLogin;
use App\Livewire\Auth\ClientRegister;
use App\Livewire\Auth\Login;
use App\Livewire\Lineas;
use App\Livewire\Novedades;
use App\Livewire\PlatformsMaster;
use App\Livewire\Settings;
use App\Livewire\Sorteos;
use App\Livewire\Tickets;
use App\Livewire\Users\UsersIndex;
use App\Livewire\Ventas;
use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Platform;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Support\Roles;
use App\Traits\HasLinePermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthPermissionSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_line_switch_requires_authentication(): void
    {
        $line = Line::create(['name' => 'Linea Cerrada', 'status' => 'active']);

        $this->post(route('admin.session.line', $line->id))
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
            ->post(route('admin.session.line', $otherLine->id))
            ->assertForbidden();
    }

    public function test_agent_without_client_permission_cannot_access_clients(): void
    {
        [$user, $agent, $line] = $this->agentWithLine();

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('admin.clientes'))
            ->assertForbidden();
    }

    public function test_agent_with_client_permission_can_access_clients(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::USER_READ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('admin.clientes'))
            ->assertOk();
    }

    public function test_agent_without_chat_permission_cannot_access_chats(): void
    {
        [$user, $agent, $line] = $this->agentWithLine();

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('admin.chats'))
            ->assertForbidden();
    }

    public function test_agent_with_ticket_permission_can_access_chats(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::TICKET_READ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('admin.chats'))
            ->assertOk();
    }

    public function test_sales_screen_uses_line_edit_permission(): void
    {
        [$user, $agent, $line] = $this->agentWithLine();

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('admin.ventas'))
            ->assertForbidden();

        LineAgentPermission::create([
            'line_id' => $line->id,
            'agent_id' => $agent->id,
            'permission' => Permissions::LINE_EDIT,
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('admin.ventas'))
            ->assertOk();
    }

    public function test_editor_home_requires_dedicated_home_permission(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([
            Permissions::BONO_UPDATE,
            Permissions::LINE_EDIT,
        ]);
        [, $vendor] = $this->cajeroVendor();
        $user->forceFill(['vendor_id' => $vendor->id])->save();
        $agent->forceFill(['vendor_id' => $vendor->id])->save();
        $line->forceFill(['vendor_id' => $vendor->id])->save();
        LineAgent::where('agent_id', $agent->id)->update(['vendor_id' => $vendor->id]);
        LineAgentPermission::where('agent_id', $agent->id)->update(['vendor_id' => $vendor->id]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id, 'active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('admin.editor.inicio'))
            ->assertForbidden();

        LineAgentPermission::create([
            'line_id' => $line->id,
            'agent_id' => $agent->id,
            'permission' => Permissions::HOME_EDIT,
        ]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id, 'active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('admin.editor.inicio'))
            ->assertOk();
    }

    public function test_non_admin_cannot_access_platform_manager_even_with_line_permission(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::PLATFORM_READ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id])
            ->get(route('admin.plataformas'))
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
            'admin.dashboard',
            'admin.clientes',
            'admin.agentes',
            'admin.lineas',
            'admin.plataformas',
            'admin.editor.inicio',
            'admin.novedades',
            'admin.bonos',
            'admin.ventas',
            'admin.sorteos',
            'admin.tickets',
            'admin.configuracion',
        ] as $route) {
            $this->actingAs($user)
                ->withSession(['active_vendor_id' => $vendor->id])
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_cajero_with_assigned_vendor_but_missing_vendor_id_can_login_to_dashboard(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $user->forceFill(['vendor_id' => null])->save();

        Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Vendor '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        Livewire::test(Login::class)
            ->set('username', $user->username)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame($vendor->id, session('active_vendor_id'));
        $this->assertSame($vendor->id, $user->fresh()->vendor_id);

        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_cajero_can_login_with_email_and_stale_vendor_session_is_replaced(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        [, $staleVendor] = $this->cajeroVendor();

        $this->withSession(['active_vendor_id' => $staleVendor->id]);

        Livewire::test(Login::class)
            ->set('username', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame($vendor->id, session('active_vendor_id'));
        $this->assertNull(session('active_agent_id'));
        $this->assertNull(session('active_line_id'));
    }

    public function test_cajero_login_prefers_assigned_vendor_over_stale_user_vendor_id(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        [, $staleVendor] = $this->cajeroVendor();
        $user->forceFill(['vendor_id' => $staleVendor->id])->save();

        Livewire::test(Login::class)
            ->set('username', $user->username)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame($vendor->id, session('active_vendor_id'));
        $this->assertSame($vendor->id, $user->fresh()->vendor_id);
    }

    public function test_cajero_with_inactive_vendor_cannot_login_to_panel(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $vendor->update(['is_active' => false]);

        Livewire::test(Login::class)
            ->set('username', $user->username)
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['username']);

        $this->assertGuest();
        $this->assertNull(session('active_vendor_id'));
    }

    public function test_cajero_direct_dashboard_request_repairs_missing_vendor_id(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $user->forceFill(['vendor_id' => null])->save();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->assertSame($vendor->id, session('active_vendor_id'));
        $this->assertSame($vendor->id, $user->fresh()->vendor_id);
    }

    public function test_cajero_dashboard_drops_stale_active_line_from_another_vendor(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();
        $otherLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Stale Dashboard '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($user)
            ->withSession([
                'active_vendor_id' => $vendor->id,
                'active_line_id' => $otherLine->id,
            ])
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->assertNull(session('active_line_id'));
    }

    public function test_dashboard_stale_ticket_alert_links_to_admin_tickets(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $clientRole = $this->role(Roles::CLIENTE, 'Cliente');
        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Alertas '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);
        $client = User::factory()->create([
            'role_id' => $clientRole->id,
            'vendor_id' => $vendor->id,
            'username' => 'cliente_alerta_'.uniqid(),
            'status' => 'active',
        ]);

        Ticket::create([
            'vendor_id' => $vendor->id,
            'user_id' => $client->id,
            'line_id' => $line->id,
            'subject' => 'Ticket viejo',
            'status' => 'open',
            'priority' => 'medium',
            'created_at' => now()->subHours(3),
            'updated_at' => now()->subHours(3),
        ]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.tickets'), false);
    }

    public function test_admin_can_view_panel_without_vendor_context(): void
    {
        $admin = User::factory()->create([
            'role_id' => $this->role(Roles::ADMIN, 'Admin')->id,
            'username' => 'admin_'.uniqid(),
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_without_vendor_context_sees_all_operational_data(): void
    {
        [$adminVendorUser, $adminVendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();

        $admin = User::factory()->create([
            'role_id' => $this->role(Roles::ADMIN, 'Admin')->id,
            'username' => 'admin_'.uniqid(),
            'status' => 'active',
        ]);

        $visibleLineName = 'Linea Vendor Visible '.uniqid();
        $hiddenLineName = 'Linea Vendor Oculta '.uniqid();

        Line::create([
            'vendor_id' => $adminVendor->id,
            'name' => $visibleLineName,
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => $hiddenLineName,
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.lineas'))
            ->assertOk()
            ->assertSee(strtoupper($visibleLineName))
            ->assertSee(strtoupper($hiddenLineName));
    }

    public function test_admin_must_select_vendor_before_creating_vendor_scoped_content(): void
    {
        $admin = User::factory()->create([
            'role_id' => $this->role(Roles::ADMIN, 'Admin')->id,
            'username' => 'admin_'.uniqid(),
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        Livewire::test(Lineas::class)
            ->set('name', 'Linea Sin Vendor')
            ->set('status', 'active')
            ->call('saveLine')
            ->assertForbidden();
    }

    public function test_admin_creates_vendor_scoped_content_after_selecting_vendor(): void
    {
        $admin = User::factory()->create([
            'role_id' => $this->role(Roles::ADMIN, 'Admin')->id,
            'username' => 'admin_'.uniqid(),
            'status' => 'active',
        ]);
        [, $vendor] = $this->cajeroVendor();

        $this->actingAs($admin)->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Lineas::class)
            ->set('name', 'Linea Con Vendor')
            ->set('status', 'active')
            ->call('saveLine')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('lines', [
            'vendor_id' => $vendor->id,
            'name' => 'Linea Con Vendor',
        ]);
    }

    public function test_cajero_cannot_access_global_vendor_manager(): void
    {
        [$user, $vendor] = $this->cajeroVendor();

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->get(route('admin.cajeros'))
            ->assertForbidden();
    }

    public function test_cajero_with_inactive_vendor_cannot_fall_back_to_global_scope(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $vendor->update(['is_active' => false]);

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->get(route('admin.dashboard'))
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
            ->post(route('admin.session.line', $line->id))
            ->assertRedirect();

        $this->assertSame($line->id, session('active_line_id'));

        $this->actingAs($user)
            ->withSession(['active_vendor_id' => $vendor->id, 'active_line_id' => $line->id])
            ->post(route('admin.session.line', 0))
            ->assertRedirect();

        $this->assertNull(session('active_line_id'));
    }

    public function test_admin_can_switch_active_vendor_from_sidebar(): void
    {
        $adminRole = $this->role(Roles::ADMIN, 'Administrador');
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_vendor_switch_'.uniqid(),
            'status' => 'active',
        ]);
        [, $vendor] = $this->cajeroVendor();

        $this->actingAs($admin)
            ->from(route('admin.cajeros'))
            ->post(route('admin.session.vendor', $vendor->id))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertSame($vendor->id, session('active_vendor_id'));
        $this->assertNull(session('active_line_id'));
    }

    public function test_admin_sidebar_lines_are_limited_to_active_vendor(): void
    {
        $adminRole = $this->role(Roles::ADMIN, 'Administrador');
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_vendor_lines_'.uniqid(),
            'status' => 'active',
        ]);
        [, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();
        $visibleLine = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Vendor Visible '.uniqid(),
            'status' => 'active',
        ]);
        $hiddenLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Vendor Oculta '.uniqid(),
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee($visibleLine->name)
            ->assertDontSee($hiddenLine->name);
    }

    public function test_admin_active_vendor_filters_vendor_scoped_module_items(): void
    {
        $admin = User::factory()->create([
            'role_id' => $this->role(Roles::ADMIN, 'Administrador')->id,
            'username' => 'admin_vendor_filter_'.uniqid(),
            'status' => 'active',
        ]);
        [, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();
        $clientRole = $this->role(Roles::CLIENTE, 'Cliente');

        $visibleLine = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Visible '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);
        $hiddenLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Oculta '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $visibleClient = User::factory()->create([
            'role_id' => $clientRole->id,
            'vendor_id' => $vendor->id,
            'line_id' => $visibleLine->id,
            'username' => 'cliente_visible_'.uniqid(),
            'status' => 'active',
        ]);
        $hiddenClient = User::factory()->create([
            'role_id' => $clientRole->id,
            'vendor_id' => $otherVendor->id,
            'line_id' => $hiddenLine->id,
            'username' => 'cliente_oculto_'.uniqid(),
            'status' => 'active',
        ]);

        $visibleAgent = Agent::create([
            'vendor_id' => $vendor->id,
            'name' => 'Agente Visible '.uniqid(),
            'email' => 'visible-agent-'.uniqid().'@example.test',
            'password' => Hash::make('password'),
            'cargo' => 'agente',
            'status' => 'active',
        ]);
        $hiddenAgent = Agent::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Agente Oculto '.uniqid(),
            'email' => 'hidden-agent-'.uniqid().'@example.test',
            'password' => Hash::make('password'),
            'cargo' => 'agente',
            'status' => 'active',
        ]);
        LineAgent::create(['vendor_id' => $vendor->id, 'line_id' => $visibleLine->id, 'agent_id' => $visibleAgent->id, 'role' => LineRoles::MIEMBRO, 'is_active' => true]);
        LineAgent::create(['vendor_id' => $otherVendor->id, 'line_id' => $hiddenLine->id, 'agent_id' => $hiddenAgent->id, 'role' => LineRoles::MIEMBRO, 'is_active' => true]);

        $visiblePlatform = Platform::create(['vendor_id' => $vendor->id, 'name' => 'Platform Visible '.uniqid(), 'slug' => 'platform-visible-'.uniqid(), 'is_active' => true]);
        $hiddenPlatform = Platform::create(['vendor_id' => $otherVendor->id, 'name' => 'Platform Hidden '.uniqid(), 'slug' => 'platform-hidden-'.uniqid(), 'is_active' => true]);
        $visibleSale = Sale::create(['vendor_id' => $vendor->id, 'line_id' => $visibleLine->id, 'platform_id' => $visiblePlatform->id, 'fecha_inicio' => now(), 'fecha_fin' => now(), 'monto_fichas' => 100]);
        $hiddenSale = Sale::create(['vendor_id' => $otherVendor->id, 'line_id' => $hiddenLine->id, 'platform_id' => $hiddenPlatform->id, 'fecha_inicio' => now(), 'fecha_fin' => now(), 'monto_fichas' => 200]);

        $this->actingAs($admin)->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Lineas::class)
            ->assertViewHas('activeLines', fn ($lines) => $lines->pluck('id')->contains($visibleLine->id) && ! $lines->pluck('id')->contains($hiddenLine->id));

        Livewire::test(UsersIndex::class)
            ->assertViewHas('users', fn ($users) => $users->pluck('id')->contains($visibleClient->id) && ! $users->pluck('id')->contains($hiddenClient->id));

        Livewire::test(Agentes::class)
            ->assertViewHas('agents', fn ($agents) => $agents->pluck('id')->contains($visibleAgent->id) && ! $agents->pluck('id')->contains($hiddenAgent->id));

        Livewire::test(PlatformsMaster::class)
            ->assertViewHas('platforms', fn ($platforms) => $platforms->pluck('id')->contains($visiblePlatform->id) && ! $platforms->pluck('id')->contains($hiddenPlatform->id));

        Livewire::test(Ventas::class)
            ->assertViewHas('sales', fn ($sales) => $sales->pluck('id')->contains($visibleSale->id) && ! $sales->pluck('id')->contains($hiddenSale->id));
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
            ->post(route('admin.session.line', $otherLine->id))
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
            ->post(route('admin.session.line', $line->id))
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
            use HasLinePermissions;
        };

        $this->assertNull($resolver->lineIdForScopedCreate());

        session(['active_line_id' => $line->id]);

        $this->assertSame($line->id, $resolver->lineIdForScopedCreate());
    }

    public function test_admin_active_vendor_cannot_operate_direct_line_from_other_vendor(): void
    {
        $admin = User::factory()->create([
            'role_id' => $this->role(Roles::ADMIN, 'Admin')->id,
            'username' => 'admin_'.uniqid(),
            'status' => 'active',
        ]);
        [, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();

        $otherLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Directa Ajena '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->get(route('admin.lineas.detalle', $otherLine->id))
            ->assertForbidden();
    }

    public function test_active_vendor_scoped_create_rejects_stale_line_from_other_vendor(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();

        $otherLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Stale Ajena '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($user)
            ->withSession([
                'active_vendor_id' => $vendor->id,
                'active_line_id' => $otherLine->id,
            ]);

        $resolver = new class
        {
            use HasLinePermissions;
        };

        $this->assertNull($resolver->lineIdForScopedCreate());
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

        Livewire::test(Tickets::class)
            ->assertSee('Ticket Vendor Propio')
            ->assertDontSee('Ticket Vendor Ajeno');

        Livewire::test(Sorteos::class)
            ->assertSee('Sorteo Vendor Propio')
            ->assertDontSee('Sorteo Vendor Ajeno');
    }

    public function test_admin_active_vendor_cannot_create_ticket_for_other_vendor_client(): void
    {
        $clientRole = $this->role(Roles::CLIENTE, 'Cliente');
        $admin = User::factory()->create([
            'role_id' => $this->role(Roles::ADMIN, 'Admin')->id,
            'username' => 'admin_ticket_vendor',
            'status' => 'active',
        ]);
        [, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();
        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Ticket Vendor '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);
        $otherClient = User::factory()->create([
            'role_id' => $clientRole->id,
            'vendor_id' => $otherVendor->id,
            'username' => 'cliente_ticket_ajeno',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->withSession(['active_vendor_id' => $vendor->id, 'active_line_id' => $line->id]);

        Livewire::test(Tickets::class)
            ->set('createSubject', 'Ticket cruzado')
            ->set('createUserId', (string) $otherClient->id)
            ->set('createMessage', 'No deberia crearse')
            ->call('createTicket')
            ->assertHasErrors(['createUserId']);

        $this->assertDatabaseMissing('tickets', [
            'subject' => 'Ticket cruzado',
            'user_id' => $otherClient->id,
        ]);
    }

    public function test_admin_active_vendor_cannot_delete_other_vendor_post(): void
    {
        $admin = User::factory()->create([
            'role_id' => $this->role(Roles::ADMIN, 'Admin')->id,
            'username' => 'admin_post_vendor',
            'status' => 'active',
        ]);
        [, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();
        $otherPost = Post::withoutGlobalScopes()->create([
            'vendor_id' => $otherVendor->id,
            'title' => 'Post Ajeno',
            'slug' => 'post-ajeno-'.uniqid(),
            'status' => Post::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Novedades::class)
            ->call('deletePost', $otherPost->id)
            ->assertForbidden();

        $this->assertDatabaseHas('posts', [
            'id' => $otherPost->id,
            'vendor_id' => $otherVendor->id,
        ]);
    }

    public function test_editor_home_uses_global_home_sections(): void
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
            ->get(route('admin.editor.inicio'))
            ->assertOk();

        $this->actingAs($secondUser)
            ->withSession(['active_vendor_id' => $secondVendor->id])
            ->get(route('admin.editor.inicio'))
            ->assertOk();

        $this->assertDatabaseHas('home_sections', [
            'vendor_id' => null,
            'section_key' => 'como-empezar',
        ]);
        $this->assertDatabaseMissing('home_sections', ['vendor_id' => $firstVendor->id]);
        $this->assertDatabaseMissing('home_sections', ['vendor_id' => $secondVendor->id]);
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

    public function test_admin_agent_line_selector_requires_active_vendor(): void
    {
        $admin = User::factory()->create([
            'role_id' => $this->role(Roles::ADMIN, 'Admin')->id,
            'status' => 'active',
        ]);

        [, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();

        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Admin Propia '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $otherLine = Line::create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Linea Admin Ajena '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        $this->actingAs($admin);

        Livewire::test(Agentes::class)
            ->assertViewHas('lines', fn ($lines) => $lines->isEmpty())
            ->set('name', 'Agente Sin Vendor')
            ->set('email', 'agente-sin-vendor@example.test')
            ->set('password', 'secret123')
            ->set('avatar', 'avatar_adventurer__red-picantes-01')
            ->set('cargo', 'agente')
            ->set('lineIds', [$line->id])
            ->call('saveAgent')
            ->assertForbidden();

        $this->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Agentes::class)
            ->assertViewHas('lines', fn ($lines) => $lines->pluck('id')->all() === [$line->id])
            ->set('name', 'Agente Vendor Ajeno')
            ->set('email', 'agente-vendor-ajeno@example.test')
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
            ->get(route('admin.lineas.detalle', $otherLine->id))
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
            ->get(route('admin.dashboard'))
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

    public function test_panel_admin_login_ignores_stale_public_vendor_context(): void
    {
        $role = $this->role(Roles::ADMIN, 'Admin');
        $admin = User::factory()->create([
            'role_id' => $role->id,
            'username' => 'admin_vendor_stale',
            'email' => 'admin-vendor-stale@test.local',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        [, $vendor] = $this->cajeroVendor();

        $this->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Login::class)
            ->set('username', 'admin_vendor_stale')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session('active_vendor_id'));
    }

    public function test_client_login_uses_clients_own_vendor_when_public_context_is_stale(): void
    {
        $role = $this->role(Roles::CLIENTE, 'Cliente');
        [, $staleVendor] = $this->cajeroVendor();
        [, $clientVendor] = $this->cajeroVendor();
        $client = User::factory()->create([
            'role_id' => $role->id,
            'vendor_id' => $clientVendor->id,
            'username' => 'cliente_vendor_stale',
            'email' => 'cliente-vendor-stale@test.local',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $this->withSession(['active_vendor_id' => $staleVendor->id]);

        Livewire::test(ClientLogin::class)
            ->set('username', 'cliente_vendor_stale')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('client.account'));

        $this->assertAuthenticatedAs($client);
        $this->assertSame($clientVendor->id, session('active_vendor_id'));
    }

    public function test_client_without_active_vendor_cannot_login(): void
    {
        $role = $this->role(Roles::CLIENTE, 'Cliente');
        User::factory()->create([
            'role_id' => $role->id,
            'username' => 'cliente_sin_vendor',
            'email' => 'cliente-sin-vendor@test.local',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        Livewire::test(ClientLogin::class)
            ->set('username', 'cliente_sin_vendor')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['username']);

        $this->assertGuest();
    }

    public function test_client_registration_requires_public_vendor_context(): void
    {
        Livewire::test(ClientRegister::class)
            ->set('name', 'Cliente')
            ->set('username', 'cliente_registro_sin_vendor')
            ->set('email', 'cliente-registro-sin-vendor@test.local')
            ->set('password', 'Password123')
            ->set('password_confirmation', 'Password123')
            ->call('register')
            ->assertHasErrors(['username']);
    }

    public function test_client_registration_uses_vendor_from_slug_context(): void
    {
        [, $vendor] = $this->cajeroVendor();
        Event::fake();

        $this->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(ClientRegister::class)
            ->set('name', 'Cliente')
            ->set('username', 'cliente_registro_vendor')
            ->set('email', 'cliente-registro-vendor@test.local')
            ->set('password', 'Password123')
            ->set('password_confirmation', 'Password123')
            ->call('register')
            ->assertRedirect(route('client.account'));

        $this->assertDatabaseHas('users', [
            'vendor_id' => $vendor->id,
            'username' => 'cliente_registro_vendor',
        ]);
    }

    public function test_public_lines_without_slug_show_all_vendor_lines(): void
    {
        [, $vendor] = $this->cajeroVendor();
        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Public Vendor '.uniqid(),
            'status' => 'active',
        ]);

        $this->get(route('frontend.lineas'))
            ->assertOk()
            ->assertSee($line->name);
    }

    public function test_public_lines_with_slug_context_show_vendor_lines(): void
    {
        [, $vendor] = $this->cajeroVendor();
        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea Public Vendor '.uniqid(),
            'status' => 'active',
        ]);

        $this->withSession(['active_vendor_id' => $vendor->id])
            ->get(route('frontend.lineas'))
            ->assertOk()
            ->assertSee($line->name);
    }

    public function test_logged_client_can_open_another_vendor_public_slug(): void
    {
        $role = $this->role(Roles::CLIENTE, 'Cliente');
        [, $vendor] = $this->cajeroVendor();
        [, $otherVendor] = $this->cajeroVendor();
        $client = User::factory()->create([
            'role_id' => $role->id,
            'vendor_id' => $vendor->id,
            'username' => 'cliente_slug_ajeno',
            'email' => 'cliente-slug-ajeno@test.local',
            'status' => 'active',
        ]);

        $this->actingAs($client)
            ->withSession(['active_vendor_id' => $vendor->id])
            ->get(route('frontend.cajero.inicio', $otherVendor->slug))
            ->assertOk();

        $this->assertSame($vendor->id, session('active_vendor_id'));
    }

    public function test_logged_client_can_open_own_vendor_public_slug(): void
    {
        $role = $this->role(Roles::CLIENTE, 'Cliente');
        [, $vendor] = $this->cajeroVendor();
        $client = User::factory()->create([
            'role_id' => $role->id,
            'vendor_id' => $vendor->id,
            'username' => 'cliente_slug_propio',
            'email' => 'cliente-slug-propio@test.local',
            'status' => 'active',
        ]);

        $this->actingAs($client)
            ->get(route('frontend.cajero.inicio', $vendor->slug))
            ->assertOk();

        $this->assertSame($vendor->id, session('active_vendor_id'));
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
