<?php

namespace Tests\Feature;

use App\Livewire\Bonos;
use App\Livewire\Auth\AdminForgotPassword;
use App\Livewire\Auth\ClientForgotPassword;
use App\Livewire\Frontend\PublicRaffle;
use App\Livewire\Novedades;
use App\Livewire\Tickets;
use App\Models\Agent;
use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuditedFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_blog_posts_get_a_publication_date(): void
    {
        $admin = $this->userWithRole(Roles::ADMIN);
        $this->actingAs($admin);

        Livewire::test(Novedades::class)
            ->set('title', 'Post visible')
            ->set('content', 'Contenido')
            ->set('excerpt', 'Resumen')
            ->set('status', Post::STATUS_PUBLISHED)
            ->call('savePost')
            ->assertHasNoErrors();

        $this->assertNotNull(Post::withoutGlobalScopes()->where('title', 'Post visible')->value('published_at'));
    }

    public function test_current_active_raffle_ignores_future_active_raffles(): void
    {
        $line = Line::create(['name' => 'Linea', 'status' => 'active']);
        $future = Raffle::withoutGlobalScopes()->create([
            'title' => 'Activo futuro',
            'status' => 'active',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'start_number' => 1,
            'end_number' => 10,
            'numbers_limit' => 10,
            'line_id' => $line->id,
        ]);
        $current = Raffle::withoutGlobalScopes()->create([
            'title' => 'Activo vigente',
            'status' => 'active',
            'start_date' => now()->subHour(),
            'end_date' => now()->addHour(),
            'start_number' => 1,
            'end_number' => 10,
            'numbers_limit' => 10,
            'line_id' => $line->id,
        ]);
        $future->lines()->sync([$line->id]);
        $current->lines()->sync([$line->id]);

        $this->assertSame($current->id, app(PublicRaffle::class)->getActiveRaffle()?->id);
    }

    public function test_agents_can_see_unassigned_client_tickets(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::TICKET_READ]);
        $client = $this->userWithRole(Roles::CLIENTE);

        Ticket::create([
            'user_id' => $client->id,
            'line_id' => null,
            'subject' => 'Ticket sin linea',
            'category' => 'otro',
            'status' => 'open',
            'priority' => 'medium',
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id]);

        Livewire::test(Tickets::class)
            ->assertSee('Ticket sin linea');
    }

    public function test_bonus_assignment_skips_clients_outside_bonus_line(): void
    {
        [$user, $agent, $line] = $this->agentWithLine([Permissions::BONO_READ]);
        $otherLine = Line::create(['name' => 'Otra linea', 'status' => 'active']);
        $client = $this->userWithRole(Roles::CLIENTE, [
            'username' => 'cliente_otro',
            'status' => 'active',
            'line_id' => $otherLine->id,
        ]);
        $bonus = Bonus::withoutGlobalScopes()->create([
            'title' => 'Bono linea',
            'code' => 'BONO-LINEA',
            'description' => 'Test',
            'type' => 'general',
            'status' => 'active',
            'start_date' => now()->subHour(),
            'end_date' => now()->addHour(),
            'line_id' => $line->id,
            'per_user_limit' => 1,
        ]);

        $this->actingAs($user)
            ->withSession(['active_agent_id' => $agent->id, 'active_line_id' => $line->id]);

        Livewire::test(Bonos::class)
            ->set('selectedBonusId', $bonus->id)
            ->set('assignLineId', (string) $line->id)
            ->set('assignUserIds', [$client->id])
            ->call('assignToUser')
            ->assertHasNoErrors();

        $this->assertSame(0, BonusAssignment::where('bonus_id', $bonus->id)->count());
    }

    public function test_authenticated_client_is_redirected_to_client_account_from_guest_pages(): void
    {
        $client = $this->userWithRole(Roles::CLIENTE);

        $this->actingAs($client)
            ->get(route('login'))
            ->assertRedirect(route('client.account'));
    }

    public function test_client_password_recovery_rejects_panel_accounts(): void
    {
        $admin = $this->userWithRole(Roles::ADMIN, ['email' => 'admin@example.test']);

        Livewire::test(ClientForgotPassword::class)
            ->set('email', $admin->email)
            ->call('sendResetLink')
            ->assertHasErrors(['email']);
    }

    public function test_admin_password_recovery_rejects_client_accounts(): void
    {
        $client = $this->userWithRole(Roles::CLIENTE, ['email' => 'cliente@example.test']);

        Livewire::test(AdminForgotPassword::class)
            ->set('email', $client->email)
            ->call('sendResetLink')
            ->assertHasErrors(['email']);
    }

    public function test_public_blog_detail_ignores_dashboard_line_scope(): void
    {
        $line = Line::create(['name' => 'Linea blog', 'status' => 'active']);
        $otherLine = Line::create(['name' => 'Otra linea blog', 'status' => 'active']);
        $post = Post::withoutGlobalScopes()->create([
            'title' => 'Post publico multi linea',
            'slug' => 'post-publico-multi-linea',
            'content' => 'Contenido visible',
            'excerpt' => 'Resumen',
            'status' => Post::STATUS_PUBLISHED,
            'published_at' => now(),
            'line_id' => $otherLine->id,
        ]);

        $this->withSession(['active_line_id' => $line->id])
            ->get(route('frontend.blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Post publico multi linea');
    }

    public function test_public_agent_registration_route_is_not_available(): void
    {
        $this->get('/admin/register')->assertNotFound();
    }

    private function agentWithLine(array $permissions = []): array
    {
        $user = $this->userWithRole(Roles::AGENTE);
        $agent = Agent::create([
            'user_id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'cargo' => 'agente',
            'status' => 'active',
        ]);
        $line = Line::create(['name' => 'Linea '.uniqid(), 'status' => 'active']);

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

    private function userWithRole(string $roleName, array $attributes = []): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['label' => ucfirst($roleName)]);

        return User::factory()->create(array_merge([
            'role_id' => $role->id,
            'username' => $roleName.'_'.uniqid(),
            'status' => 'active',
        ], $attributes));
    }
}
