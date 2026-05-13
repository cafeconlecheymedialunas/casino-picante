<?php

namespace Tests\Feature;

use App\Livewire\Sorteos;
use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Role;
use App\Models\User;
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SorteosNumberCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => Roles::ADMIN],
            ['label' => 'Administrador']
        );
        $this->actingAs(User::factory()->create(['role_id' => $role->id]));
    }

    public function test_raffle_can_be_created_without_optional_prizes(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);

        Livewire::test(Sorteos::class)
            ->set('title', 'Sorteo Nuevo')
            ->set('description', 'Descripcion')
            ->set('status', 'active')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '10:00')
            ->set('end_date', now()->addDay()->format('Y-m-d'))
            ->set('end_time', '22:00')
            ->set('start_number', 1)
            ->set('numbersLimit', '100')
            ->set('lineIds', [$line->id])
            ->set('prizes', [['position' => '1', 'name' => '', 'image' => '']])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('raffles', [
            'title' => 'Sorteo Nuevo',
            'line_id' => $line->id,
            'numbers_limit' => 100,
        ]);
        $this->assertSame([], Raffle::withoutGlobalScopes()->where('title', 'Sorteo Nuevo')->first()->prizes);
    }

    public function test_raffle_changes_are_saved_from_edit_modal(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);

        $raffle = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo Viejo',
            'description' => 'Antes',
            'status' => 'inactive',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'start_number' => 1,
            'end_number' => 20,
            'numbers_limit' => 20,
            'line_id' => $line->id,
            'prizes' => [],
        ]);
        $raffle->lines()->sync([$line->id]);

        Livewire::test(Sorteos::class)
            ->call('openEdit', $raffle->id)
            ->set('title', 'Sorteo Editado')
            ->set('description', 'Despues')
            ->set('status', 'active')
            ->set('numbersLimit', '50')
            ->set('prizes', [['position' => '1', 'name' => '', 'image' => '']])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('raffles', [
            'id' => $raffle->id,
            'title' => 'Sorteo Editado',
            'description' => 'Despues',
            'status' => 'active',
            'numbers_limit' => 50,
        ]);
    }

    public function test_raffle_save_requires_a_number_limit_unless_it_is_unlimited(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);

        Livewire::test(Sorteos::class)
            ->set('title', 'Sorteo Nuevo')
            ->set('status', 'active')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '10:00')
            ->set('end_date', now()->addDay()->format('Y-m-d'))
            ->set('end_time', '22:00')
            ->set('start_number', 1)
            ->set('numbersLimit', '')
            ->set('lineIds', [$line->id])
            ->call('save')
            ->assertHasErrors(['numbersLimit']);
    }

    public function test_raffle_can_be_saved_as_unlimited_without_number_limit(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);

        Livewire::test(Sorteos::class)
            ->set('title', 'Sorteo Ilimitado')
            ->set('status', 'active')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '10:00')
            ->set('end_date', now()->addDay()->format('Y-m-d'))
            ->set('end_time', '22:00')
            ->set('start_number', 1)
            ->set('unlimitedNumbers', true)
            ->set('numbersLimit', '')
            ->set('lineIds', [$line->id])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('raffles', [
            'title' => 'Sorteo Ilimitado',
            'numbers_limit' => null,
        ]);
    }

    public function test_selected_numbers_can_be_assigned_and_reassigned_from_the_board(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);

        $firstClient = User::factory()->create(['username' => 'cliente_uno', 'status' => 'active']);
        $secondClient = User::factory()->create(['username' => 'cliente_dos', 'status' => 'active']);
        $line->clients()->syncWithoutDetaching([
            $firstClient->id => ['is_active' => true],
            $secondClient->id => ['is_active' => true],
        ]);

        $raffle = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo Test',
            'description' => 'Test',
            'status' => 'active',
            'start_date' => now()->subMinute(),
            'end_date' => now()->addDay(),
            'start_number' => 1,
            'end_number' => 20,
            'numbers_limit' => 20,
            'line_id' => $line->id,
        ]);
        $raffle->lines()->sync([$line->id]);

        Livewire::test(Sorteos::class)
            ->set('selectedRaffleId', $raffle->id)
            ->set('assignUserId', (string) $firstClient->id)
            ->set('selectedNumbers', [1, 2, 3])
            ->call('saveSelectedNumbers')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('raffle_numbers', [
            'raffle_id' => $raffle->id,
            'user_id' => $firstClient->id,
            'line_id' => $line->id,
            'number' => 1,
        ]);
        $this->assertSame(3, RaffleNumber::where('raffle_id', $raffle->id)->count());

        Livewire::test(Sorteos::class)
            ->set('selectedRaffleId', $raffle->id)
            ->set('assignUserId', (string) $secondClient->id)
            ->set('selectedNumbers', [2, 4])
            ->call('saveSelectedNumbers')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('raffle_numbers', [
            'raffle_id' => $raffle->id,
            'user_id' => $secondClient->id,
            'line_id' => $line->id,
            'number' => 2,
        ]);
        $this->assertDatabaseHas('raffle_numbers', [
            'raffle_id' => $raffle->id,
            'user_id' => $secondClient->id,
            'line_id' => $line->id,
            'number' => 4,
        ]);
        $this->assertSame(4, RaffleNumber::where('raffle_id', $raffle->id)->count());
    }

    public function test_active_client_without_line_can_be_assigned_to_raffle_number(): void
    {
        $clientRole = Role::firstOrCreate(
            ['name' => Roles::CLIENTE],
            ['label' => 'Cliente']
        );
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);
        $client = User::factory()->create([
            'role_id' => $clientRole->id,
            'username' => 'cliente_suelto',
            'line_id' => null,
            'status' => 'active',
        ]);

        $raffle = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo Test',
            'description' => 'Test',
            'status' => 'active',
            'start_date' => now()->subMinute(),
            'end_date' => now()->addDay(),
            'start_number' => 1,
            'end_number' => 20,
            'numbers_limit' => 20,
            'line_id' => $line->id,
        ]);
        $raffle->lines()->sync([$line->id]);

        Livewire::test(Sorteos::class)
            ->set('selectedRaffleId', $raffle->id)
            ->assertSee('cliente_suelto')
            ->set('assignUserId', (string) $client->id)
            ->set('selectedNumbers', [5])
            ->call('saveSelectedNumbers')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('line_clients', [
            'line_id' => $line->id,
            'user_id' => $client->id,
        ]);
        $this->assertDatabaseHas('raffle_numbers', [
            'raffle_id' => $raffle->id,
            'user_id' => $client->id,
            'line_id' => $line->id,
            'number' => 5,
        ]);
    }

    public function test_agent_with_read_permission_can_assign_selected_numbers(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);
        $agent = Agent::create([
            'username' => 'agente_test',
            'name' => 'Agente',
            'email' => 'agente@test.local',
            'password' => 'secret',
            'cargo' => 'agente',
            'status' => 'active',
        ]);
        LineAgent::create([
            'line_id' => $line->id,
            'agent_id' => $agent->id,
            'role' => 'miembro',
            'is_active' => true,
        ]);
        LineAgentPermission::create([
            'line_id' => $line->id,
            'agent_id' => $agent->id,
            'permission' => Permissions::SORTEO_READ,
        ]);

        $client = User::factory()->create(['username' => 'cliente_uno', 'status' => 'active']);
        $line->clients()->syncWithoutDetaching([$client->id => ['is_active' => true]]);
        $raffle = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo Test',
            'description' => 'Test',
            'status' => 'active',
            'start_date' => now()->subMinute(),
            'end_date' => now()->addDay(),
            'start_number' => 1,
            'end_number' => 20,
            'numbers_limit' => 20,
            'line_id' => $line->id,
        ]);
        $raffle->lines()->sync([$line->id]);

        $this->withSession([
            'active_agent_id' => $agent->id,
            'active_line_id' => $line->id,
        ]);

        Livewire::test(Sorteos::class)
            ->set('selectedRaffleId', $raffle->id)
            ->set('assignUserId', (string) $client->id)
            ->set('selectedNumbers', [6])
            ->call('saveSelectedNumbers')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('raffle_numbers', [
            'raffle_id' => $raffle->id,
            'user_id' => $client->id,
            'line_id' => $line->id,
            'number' => 6,
        ]);
    }

    public function test_board_click_toggles_number_selection(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);

        $raffle = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo Test',
            'description' => 'Test',
            'status' => 'active',
            'start_date' => now()->subMinute(),
            'end_date' => now()->addDay(),
            'start_number' => 1,
            'end_number' => 20,
            'numbers_limit' => 20,
            'line_id' => $line->id,
        ]);
        $raffle->lines()->sync([$line->id]);

        Livewire::test(Sorteos::class)
            ->set('selectedRaffleId', $raffle->id)
            ->call('toggleNumber', 5)
            ->assertSet('selectedNumbers', [5])
            ->call('toggleNumber', 5)
            ->assertSet('selectedNumbers', []);
    }

    public function test_board_marks_selected_numbers_even_when_hydrated_as_strings(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);

        $raffle = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo Test',
            'description' => 'Test',
            'status' => 'active',
            'start_date' => now()->subMinute(),
            'end_date' => now()->addDay(),
            'start_number' => 1,
            'end_number' => 20,
            'numbers_limit' => 20,
            'line_id' => $line->id,
        ]);
        $raffle->lines()->sync([$line->id]);

        Livewire::test(Sorteos::class)
            ->set('selectedRaffleId', $raffle->id)
            ->set('selectedNumbers', ['5'])
            ->assertSeeHtml('class="board-slot slot-free slot-selected"');
    }

    public function test_selected_occupied_numbers_can_be_unassigned(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);
        $client = User::factory()->create(['username' => 'cliente_uno', 'status' => 'active']);
        $line->clients()->syncWithoutDetaching([$client->id => ['is_active' => true]]);
        $raffle = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo Test',
            'description' => 'Test',
            'status' => 'active',
            'start_date' => now()->subMinute(),
            'end_date' => now()->addDay(),
            'start_number' => 1,
            'end_number' => 20,
            'numbers_limit' => 20,
            'line_id' => $line->id,
        ]);
        $raffle->lines()->sync([$line->id]);

        RaffleNumber::create([
            'raffle_id' => $raffle->id,
            'user_id' => $client->id,
            'line_id' => $line->id,
            'number' => 7,
        ]);

        Livewire::test(Sorteos::class)
            ->set('selectedRaffleId', $raffle->id)
            ->set('selectedNumbers', [7])
            ->call('unassignSelectedNumbers')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('raffle_numbers', [
            'raffle_id' => $raffle->id,
            'number' => 7,
        ]);
    }

    public function test_active_raffle_numbers_can_be_managed_even_outside_public_dates(): void
    {
        $line = Line::create([
            'name' => 'Linea Test',
            'status' => 'active',
        ]);
        $client = User::factory()->create(['username' => 'cliente_uno', 'status' => 'active']);
        $line->clients()->syncWithoutDetaching([$client->id => ['is_active' => true]]);
        $raffle = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo Test',
            'description' => 'Test',
            'status' => 'active',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'start_number' => 1,
            'end_number' => 20,
            'numbers_limit' => 20,
            'line_id' => $line->id,
        ]);
        $raffle->lines()->sync([$line->id]);

        Livewire::test(Sorteos::class)
            ->set('selectedRaffleId', $raffle->id)
            ->set('assignUserId', (string) $client->id)
            ->set('selectedNumbers', [9])
            ->call('saveSelectedNumbers')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('raffle_numbers', [
            'raffle_id' => $raffle->id,
            'user_id' => $client->id,
            'line_id' => $line->id,
            'number' => 9,
        ]);
    }
}
