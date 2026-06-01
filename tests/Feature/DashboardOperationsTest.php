<?php

namespace Tests\Feature;

use App\Livewire\Agentes;
use App\Livewire\Bonos;
use App\Livewire\Lineas;
use App\Livewire\Novedades;
use App\Livewire\Sorteos;
use App\Livewire\Tickets;
use App\Livewire\Users\UsersIndex;
use App\Livewire\Ventas;
use App\Models\Agent;
use App\Models\Bonus;
use App\Models\Category;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Platform;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cajero_can_run_core_dashboard_operations(): void
    {
        [$user, $vendor] = $this->cajeroVendor();
        $this->role(Roles::CLIENTE, 'Cliente');
        $this->role(Roles::AGENTE, 'Agente');

        $this->actingAs($user)->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Lineas::class)
            ->set('name', 'Linea Operativa')
            ->set('status', 'active')
            ->call('saveLine')
            ->assertHasNoErrors();

        $line = Line::where('name', 'Linea Operativa')->firstOrFail();
        session(['active_line_id' => $line->id]);

        $platform = Platform::withoutGlobalScopes()->create([
            'vendor_id' => null,
            'name' => 'Plataforma Operativa',
            'slug' => 'plataforma-operativa',
            'is_active' => true,
        ]);

        Livewire::test(UsersIndex::class)
            ->set('username', 'cliente_operativo')
            ->set('name', 'Cliente Operativo')
            ->set('email', 'cliente-operativo@example.test')
            ->set('password', 'secret123')
            ->set('avatar', 'avatar_adventurer__red-picantes-01')
            ->set('preferredLineId', $line->id)
            ->set('selectedLines', [$line->id])
            ->call('saveUser')
            ->assertHasNoErrors();

        $client = User::where('username', 'cliente_operativo')->firstOrFail();

        Livewire::test(Agentes::class)
            ->set('username', 'agente_operativo')
            ->set('name', 'Agente Operativo')
            ->set('email', 'agente-operativo@example.test')
            ->set('password', 'secret123')
            ->set('avatar', 'avatar_adventurer__red-picantes-01')
            ->set('cargo', 'agente')
            ->set('lineIds', [$line->id])
            ->call('saveAgent')
            ->assertHasNoErrors();

        $agent = Agent::where('username', 'agente_operativo')->firstOrFail();

        Livewire::test(Ventas::class)
            ->set('saleLineId', (string) $line->id)
            ->set('salePlatformId', (string) $platform->id)
            ->set('saleFechaInicio', now()->format('Y-m-d'))
            ->set('saleFechaFin', now()->format('Y-m-d'))
            ->set('saleMontoFichas', '250')
            ->call('saveSale')
            ->assertHasNoErrors();

        Livewire::test(Tickets::class)
            ->set('createSubject', 'Ticket operativo')
            ->set('createUserId', (string) $client->id)
            ->set('createMessage', 'Mensaje inicial')
            ->call('createTicket')
            ->assertHasNoErrors()
            ->set('newMessage', 'Respuesta')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->call('quickAction', 'resolved')
            ->assertHasNoErrors()
            ->call('reopenTicket')
            ->assertHasNoErrors();

        Livewire::test(Bonos::class)
            ->set('title', 'Bono Operativo')
            ->set('startDate', now()->format('Y-m-d'))
            ->set('startTime', '00:00')
            ->set('endDate', now()->addDay()->format('Y-m-d'))
            ->set('endTime', '23:59')
            ->set('lineId', (string) $line->id)
            ->set('platformId', (string) $platform->id)
            ->call('saveBonus')
            ->assertHasNoErrors();

        $bonus = Bonus::where('title', 'Bono Operativo')->firstOrFail();

        Livewire::test(Bonos::class)
            ->set('selectedBonusId', $bonus->id)
            ->set('assignLineId', (string) $line->id)
            ->set('assignUserIds', [$client->id])
            ->call('assignToUser')
            ->assertHasNoErrors();

        Livewire::test(Novedades::class)
            ->set('newCategoryName', 'Categoria Operativa')
            ->call('saveCategory')
            ->assertHasNoErrors();

        $category = Category::where('name', 'Categoria Operativa')->firstOrFail();

        Livewire::test(Novedades::class)
            ->set('title', 'Post Operativo')
            ->set('content', 'Contenido')
            ->set('excerpt', 'Resumen')
            ->set('category_id', $category->id)
            ->set('author_agent_id', $agent->id)
            ->call('savePost')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sales', ['line_id' => $line->id, 'platform_id' => $platform->id]);
        $this->assertDatabaseHas('tickets', ['subject' => 'Ticket operativo', 'user_id' => $client->id]);
        $this->assertDatabaseHas('bonus_assignments', ['bonus_id' => $bonus->id, 'user_id' => $client->id]);
        $this->assertDatabaseHas('posts', ['title' => 'Post Operativo', 'line_id' => $line->id]);
    }

    public function test_agent_cannot_create_sale_with_platform_from_another_vendor(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::LINE_EDIT]);
        [, $otherVendor] = $this->cajeroVendor();
        $otherPlatform = Platform::withoutGlobalScopes()->create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Plataforma Ajena',
            'slug' => 'plataforma-ajena',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id]);

        Livewire::test(Ventas::class)
            ->set('saleLineId', (string) $line->id)
            ->set('salePlatformId', (string) $otherPlatform->id)
            ->set('saleFechaInicio', now()->format('Y-m-d'))
            ->set('saleFechaFin', now()->format('Y-m-d'))
            ->set('saleMontoFichas', '100')
            ->call('saveSale')
            ->assertForbidden();
    }

    public function test_agent_cannot_create_bonus_with_platform_from_another_vendor(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::BONO_CREATE]);
        [, $otherVendor] = $this->cajeroVendor();
        $otherPlatform = Platform::withoutGlobalScopes()->create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Bono Plataforma Ajena',
            'slug' => 'bono-plataforma-ajena',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id]);

        Livewire::test(Bonos::class)
            ->set('title', 'Bono test')
            ->set('startDate', now()->format('Y-m-d'))
            ->set('startTime', '00:00')
            ->set('endDate', now()->addDay()->format('Y-m-d'))
            ->set('endTime', '23:59')
            ->set('lineId', (string) $line->id)
            ->set('platformId', (string) $otherPlatform->id)
            ->call('saveBonus')
            ->assertForbidden();
    }

    public function test_agent_cannot_create_raffle_with_platform_from_another_vendor(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::SORTEO_READ, Permissions::SORTEO_CREATE]);
        [, $otherVendor] = $this->cajeroVendor();
        $otherPlatform = Platform::withoutGlobalScopes()->create([
            'vendor_id' => $otherVendor->id,
            'name' => 'Sorteo Plataforma Ajena',
            'slug' => 'sorteo-plataforma-ajena',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id]);

        Livewire::test(Sorteos::class)
            ->set('title', 'Sorteo test')
            ->set('status', 'active')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '00:00')
            ->set('end_date', now()->addDay()->format('Y-m-d'))
            ->set('end_time', '23:59')
            ->set('lineIds', [$line->id])
            ->set('numbersLimit', '10')
            ->set('platform_id', (string) $otherPlatform->id)
            ->call('save')
            ->assertForbidden();
    }

    private function agentWithLine(array $permissions = []): array
    {
        [, $vendor] = $this->cajeroVendor();
        $role = $this->role(Roles::AGENTE, 'Agente');
        $user = User::factory()->create([
            'role_id' => $role->id,
            'vendor_id' => $vendor->id,
            'username' => 'agente_'.uniqid(),
            'status' => 'active',
        ]);

        $agent = Agent::create([
            'vendor_id' => $vendor->id,
            'user_id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'cargo' => 'agente',
            'status' => 'active',
        ]);

        $line = Line::create([
            'vendor_id' => $vendor->id,
            'name' => 'Linea '.uniqid(),
            'status' => 'active',
            'permissions' => Permissions::all(),
        ]);

        LineAgent::create([
            'vendor_id' => $vendor->id,
            'line_id' => $line->id,
            'agent_id' => $agent->id,
            'role' => LineRoles::MIEMBRO,
            'is_active' => true,
        ]);

        foreach ($permissions as $permission) {
            LineAgentPermission::create([
                'vendor_id' => $vendor->id,
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
