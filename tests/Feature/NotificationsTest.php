<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\DashboardNotification;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Role;
use App\Models\User;
use App\Services\NotificationService;
use App\Support\LineRoles;
use App\Support\Roles;
use App\Traits\SendsNotifications;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    private Line $line;
    private Agent $encargado;
    private Agent $miembro;

    protected function setUp(): void
    {
        parent::setUp();

        $agentRole = Role::firstOrCreate(['name' => Roles::AGENTE], ['label' => 'Agente']);
        $adminRole = Role::firstOrCreate(['name' => Roles::ADMIN], ['label' => 'Admin']);

        $encargadoUser = User::factory()->create(['role_id' => $agentRole->id]);
        $miembroUser   = User::factory()->create(['role_id' => $agentRole->id]);

        $this->encargado = Agent::create([
            'user_id'  => $encargadoUser->id,
            'username' => 'encargado_test',
            'name'     => 'Encargado',
            'email'    => 'enc@test.com',
            'password' => bcrypt('password'),
            'cargo'    => 'super_agente',
            'status'   => 'active',
        ]);

        $this->miembro = Agent::create([
            'user_id'  => $miembroUser->id,
            'username' => 'miembro_test',
            'name'     => 'Miembro',
            'email'    => 'mie@test.com',
            'password' => bcrypt('password'),
            'cargo'    => 'agente',
            'status'   => 'active',
        ]);

        $this->line = Line::create(['name' => 'Linea Test', 'status' => 'active']);

        LineAgent::create([
            'line_id'  => $this->line->id,
            'agent_id' => $this->encargado->id,
            'role'     => LineRoles::ENCARGADO,
            'is_active' => true,
        ]);

        LineAgent::create([
            'line_id'  => $this->line->id,
            'agent_id' => $this->miembro->id,
            'role'     => LineRoles::MIEMBRO,
            'is_active' => true,
        ]);
    }

    // --- NotificationService ---

    public function test_notification_service_creates_record(): void
    {
        NotificationService::send('Titulo', 'Mensaje', null, 'info', null, 'general');

        $this->assertDatabaseHas('dashboard_notifications', [
            'title'    => 'Titulo',
            'message'  => 'Mensaje',
            'agent_id' => null,
        ]);
    }

    public function test_notification_service_creates_for_agent(): void
    {
        NotificationService::send('Titulo', 'Mensaje', $this->miembro->id, 'success', null, 'general');

        $this->assertDatabaseHas('dashboard_notifications', [
            'agent_id' => $this->miembro->id,
            'title'    => 'Titulo',
        ]);
    }

    // --- broadcastToEncargados via notify() ---

    public function test_notify_from_miembro_sends_to_encargado_and_admin(): void
    {
        session(['active_agent_id' => $this->miembro->id, 'active_line_id' => $this->line->id]);

        $component = new class extends Component {
            use SendsNotifications;

            public function render() { return '<div></div>'; }

            public function act(): void
            {
                $this->notify('Accion', 'Mensaje del miembro', 'general', '/test', 'info');
            }
        };

        // Llamar directamente al trait
        $trait = new class($this->miembro->id, $this->line->id) {
            use SendsNotifications;

            public function __construct(private int $agentId, private int $lineId) {}

            public function run(): void
            {
                session(['active_agent_id' => $this->agentId, 'active_line_id' => $this->lineId]);
                $this->notify('Accion miembro', 'Detalle', 'general', '/test', 'info');
            }
        };

        $trait->run();

        // Miembro recibe la suya
        $this->assertDatabaseHas('dashboard_notifications', ['agent_id' => $this->miembro->id, 'title' => 'Accion miembro']);
        // Encargado recibe broadcast
        $this->assertDatabaseHas('dashboard_notifications', ['agent_id' => $this->encargado->id, 'title' => 'Accion miembro']);
        // Admin (null) recibe broadcast
        $this->assertDatabaseHas('dashboard_notifications', ['agent_id' => null, 'title' => 'Accion miembro']);

        $this->assertEquals(3, DashboardNotification::where('title', 'Accion miembro')->count());
    }

    public function test_notify_from_encargado_only_sends_to_self_and_admin(): void
    {
        $trait = new class($this->encargado->id, $this->line->id) {
            use SendsNotifications;

            public function __construct(private int $agentId, private int $lineId) {}

            public function run(): void
            {
                session(['active_agent_id' => $this->agentId, 'active_line_id' => $this->lineId]);
                $this->notify('Accion encargado', 'Detalle', 'general', '/test', 'info');
            }
        };

        $trait->run();

        // Encargado recibe la suya
        $this->assertDatabaseHas('dashboard_notifications', ['agent_id' => $this->encargado->id, 'title' => 'Accion encargado']);
        // Admin recibe también
        $this->assertDatabaseHas('dashboard_notifications', ['agent_id' => null, 'title' => 'Accion encargado']);
        // Miembro NO recibe
        $this->assertDatabaseMissing('dashboard_notifications', ['agent_id' => $this->miembro->id, 'title' => 'Accion encargado']);

        $this->assertEquals(2, DashboardNotification::where('title', 'Accion encargado')->count());
    }

    public function test_broadcast_skips_inactive_encargado(): void
    {
        $this->encargado->update(['status' => 'inactive']);

        $trait = new class($this->miembro->id, $this->line->id) {
            use SendsNotifications;

            public function __construct(private int $agentId, private int $lineId) {}

            public function run(): void
            {
                session(['active_agent_id' => $this->agentId, 'active_line_id' => $this->lineId]);
                $this->notify('Sin encargado activo', 'Detalle', 'general', '/test', 'info');
            }
        };

        $trait->run();

        // Encargado inactivo NO recibe
        $this->assertDatabaseMissing('dashboard_notifications', [
            'agent_id' => $this->encargado->id,
            'title'    => 'Sin encargado activo',
        ]);

        // Admin sí recibe
        $this->assertDatabaseHas('dashboard_notifications', [
            'agent_id' => null,
            'title'    => 'Sin encargado activo',
        ]);
    }

    public function test_broadcast_skips_deleted_encargado(): void
    {
        $this->encargado->delete();

        $trait = new class($this->miembro->id, $this->line->id) {
            use SendsNotifications;

            public function __construct(private int $agentId, private int $lineId) {}

            public function run(): void
            {
                session(['active_agent_id' => $this->agentId, 'active_line_id' => $this->lineId]);
                $this->notify('Encargado eliminado', 'Detalle', 'general', '/test', 'info');
            }
        };

        $trait->run();

        $this->assertEquals(0, DashboardNotification::where('agent_id', $this->encargado->id)->count());
    }

    public function test_notify_in_admin_mode_does_not_broadcast(): void
    {
        // Sin active_agent_id ni active_line_id = modo admin
        session()->forget(['active_agent_id', 'active_line_id']);

        $trait = new class {
            use SendsNotifications;

            public function run(): void
            {
                $this->notify('Admin action', 'Detalle', 'general', '/test', 'info');
            }
        };

        $trait->run();

        // Solo crea 1 notificación (para admin, agent_id=null)
        $this->assertEquals(1, DashboardNotification::where('title', 'Admin action')->count());
        $this->assertDatabaseHas('dashboard_notifications', ['agent_id' => null, 'title' => 'Admin action']);
    }

    // --- PageHeader scoping ---

    public function test_pageheader_shows_only_agent_notifications(): void
    {
        DashboardNotification::create(['agent_id' => $this->miembro->id, 'title' => 'Para miembro', 'message' => 'x', 'type' => 'info', 'module' => 'general']);
        DashboardNotification::create(['agent_id' => $this->encargado->id, 'title' => 'Para encargado', 'message' => 'x', 'type' => 'info', 'module' => 'general']);
        DashboardNotification::create(['agent_id' => null, 'title' => 'Para admin', 'message' => 'x', 'type' => 'info', 'module' => 'general']);

        $adminRole = Role::firstOrCreate(['name' => Roles::ADMIN], ['label' => 'Admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->actingAs($admin);

        session(['active_agent_id' => $this->miembro->id, 'active_line_id' => $this->line->id]);

        Livewire::test(\App\Livewire\Components\PageHeader::class)
            ->assertSee('Para miembro')
            ->assertDontSee('Para encargado')
            ->assertDontSee('Para admin');
    }

    public function test_pageheader_admin_sees_null_agent_notifications(): void
    {
        DashboardNotification::create(['agent_id' => null, 'title' => 'Para admin', 'message' => 'x', 'type' => 'info', 'module' => 'general']);
        DashboardNotification::create(['agent_id' => $this->miembro->id, 'title' => 'Para miembro', 'message' => 'x', 'type' => 'info', 'module' => 'general']);

        $adminRole = Role::firstOrCreate(['name' => Roles::ADMIN], ['label' => 'Admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->actingAs($admin);

        session()->forget(['active_agent_id', 'active_line_id']);

        Livewire::test(\App\Livewire\Components\PageHeader::class)
            ->assertSee('Para admin')
            ->assertDontSee('Para miembro');
    }

    public function test_pageheader_clears_stale_agent_session(): void
    {
        session(['active_agent_id' => 99999, 'active_line_id' => $this->line->id]);

        $adminRole = Role::firstOrCreate(['name' => Roles::ADMIN], ['label' => 'Admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Components\PageHeader::class)->call('markAllRead');

        $this->assertNull(session('active_agent_id'));
    }

    // --- DashboardNotification model ---

    public function test_mark_read_sets_timestamp(): void
    {
        $notif = DashboardNotification::create([
            'agent_id' => null,
            'title'    => 'Test',
            'message'  => 'Msg',
            'type'     => 'info',
            'module'   => 'general',
        ]);

        $this->assertNull($notif->read_at);
        $notif->markRead();
        $this->assertNotNull($notif->fresh()->read_at);
    }

    public function test_mark_read_is_idempotent(): void
    {
        $notif = DashboardNotification::create([
            'agent_id' => null,
            'title'    => 'Test',
            'message'  => 'Msg',
            'type'     => 'info',
            'module'   => 'general',
        ]);

        $notif->markRead();
        $first = $notif->fresh()->read_at;
        $notif->markRead();
        $this->assertEquals($first, $notif->fresh()->read_at);
    }
}
