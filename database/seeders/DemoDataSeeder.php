<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\CarouselItem;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Platform;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Support\LineRoles;
use App\Support\Roles;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Line::count() > 1) {
            $this->command->warn('Ya hay datos cargados — ejecutando igual, puede duplicar.');
        }

        // ── 1. PLATAFORMAS ──
        $platforms = [];
        foreach (['WhatsApp', 'Telegram', 'Web', 'Instagram', 'Facebook'] as $i => $name) {
            $platforms[] = Platform::updateOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'is_active' => true]
            );
        }
        $this->command->info('Plataformas listas.');

        // ── 2. LINEAS ──
        $lineData = [
            ['name' => 'VIP Casino', 'icon' => '🔴', 'type' => 'whatsapp', 'phone' => '+5491112345678'],
            ['name' => 'Gold Sports', 'icon' => '🟡', 'type' => 'whatsapp', 'phone' => '+5491123456789'],
            ['name' => 'Platinum Club', 'icon' => '🔵', 'type' => 'whatsapp', 'phone' => '+5491134567890'],
        ];
        $lines = [];
        foreach ($lineData as $ld) {
            $lines[] = Line::updateOrCreate(
                ['name' => $ld['name']],
                [
                    'icon' => $ld['icon'],
                    'type' => $ld['type'],
                    'phone' => $ld['phone'],
                    'status' => 'active',
                    'contact_links' => [['type' => 'whatsapp', 'value' => $ld['phone'], 'name' => 'WhatsApp']],
                ]
            );
        }
        $this->command->info('Líneas listas.');

        // ── 3. ROLES ──
        $roleAdmin = Role::firstOrCreate(['name' => Roles::ADMIN], ['display_name' => 'Administrador']);
        $roleAgent = Role::firstOrCreate(['name' => Roles::AGENTE], ['display_name' => 'Agente']);
        $roleClient = Role::firstOrCreate(['name' => Roles::CLIENTE], ['display_name' => 'Cliente']);

        // ── 4. USUARIOS (clientes) ──
        $users = [];
        $names = ['Carlos García', 'María López', 'Juan Pérez', 'Ana Rodríguez', 'Pedro Martínez',
            'Laura Sánchez', 'Diego Fernández', 'Valentina Gómez', 'Santiago Díaz', 'Camila Ruiz'];
        foreach ($names as $i => $name) {
            $parts = explode(' ', $name);
            $u = User::updateOrCreate(
                ['email' => 'cliente'.($i + 1).'@demo.com'],
                [
                    'name' => $parts[0],
                    'apellido' => $parts[1] ?? 'Apellido',
                    'username' => 'user'.($i + 1),
                    'phone' => '+54911'.str_pad((10000000 + $i), 8, '0', STR_PAD_LEFT),
                    'password' => bcrypt('demo123'),
                    'role_id' => $roleClient->id,
                    'status' => $i < 8 ? 'active' : 'blocked',
                ]
            );
            $users[] = $u;
        }
        $this->command->info('Usuarios listos.');

        // Remove existing demo data (clean slate)
        $demoEmails = ['admin@demo.com', 'carlos@demo.com', 'mariana@demo.com', 'jose@demo.com'];
        Agent::whereIn('email', $demoEmails)->delete();
        User::whereIn('email', $demoEmails)->delete();

        // ── 5. AGENTES ──
        $agents = [];
        $agentData = [
            ['name' => 'Admin', 'email' => 'admin@demo.com', 'username' => 'demoadmin', 'roleId' => $roleAdmin->id],
            ['name' => 'Carlos', 'email' => 'carlos@demo.com', 'username' => 'carlos', 'roleId' => $roleAgent->id],
            ['name' => 'Mariana', 'email' => 'mariana@demo.com', 'username' => 'mariana', 'roleId' => $roleAgent->id],
            ['name' => 'José', 'email' => 'jose@demo.com', 'username' => 'jose', 'roleId' => $roleAgent->id],
        ];
        foreach ($agentData as $ad) {
            $user = User::updateOrCreate(
                ['email' => $ad['email']],
                [
                    'name' => $ad['name'],
                    'password' => bcrypt('demo123'),
                    'username' => $ad['username'],
                    'phone' => '+5491199900'.str_pad((string) array_search($ad, $agentData), 2, '0', STR_PAD_LEFT),
                    'role_id' => $ad['roleId'],
                    'status' => 'active',
                ]
            );
            $agent = Agent::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $ad['name'],
                    'email' => $ad['email'],
                    'username' => $ad['username'],
                    'password' => bcrypt('demo123'),
                    'status' => 'active',
                ]
            );
            $agents[] = $agent;
        }
        $this->command->info('Agentes listos.');

        // ── 5. LINE_AGENTS ──
        $allPerms = LineAgentPermission::allPermissions();
        // Agente 0 (Admin) es encargado de línea 1
        // Agente 1 es miembro de línea 1
        // Agente 2 es encargado de línea 2
        // Agente 3 es miembro de línea 2

        $lineAgentRoles = [
            [1, 0, LineRoles::ENCARGADO], // line 1, agent 0 (admin user)
            [1, 1, LineRoles::MIEMBRO],    // line 1, agent 1 (carlos)
            [2, 0, LineRoles::MIEMBRO],    // line 2, agent 0
            [2, 2, LineRoles::ENCARGADO],  // line 2, agent 2 (mariana)
            [2, 3, LineRoles::MIEMBRO],    // line 2, agent 3 (josé)
            [3, 0, LineRoles::MIEMBRO],    // line 3, agent 0
        ];
        foreach ($lineAgentRoles as [$lineIdx, $agentIdx, $role]) {
            $la = LineAgent::firstOrCreate(
                ['line_id' => $lines[$lineIdx - 1]->id, 'agent_id' => $agents[$agentIdx]->id],
                ['role' => $role, 'is_active' => true]
            );
            // Give all permissions
            foreach ($allPerms as $perm) {
                LineAgentPermission::firstOrCreate([
                    'line_id' => $la->line_id,
                    'agent_id' => $la->agent_id,
                    'permission' => $perm,
                ]);
            }
        }
        $this->command->info('Line-agents listos.');

        // ── 6. POSTS (blog/novedades) ──
        $postTitles = [
            'Nuevos bonos de bienvenida disponibles',
            'Ganador del sorteo semanal de Julio',
            'Promoción especial de fin de semana',
            'Actualización de la plataforma de pagos',
            'Resultados del torneo de póker',
        ];
        foreach ($postTitles as $i => $title) {
            Post::firstOrCreate(
                ['title' => $title],
                [
                    'slug' => Str::slug($title).'-'.uniqid(),
                    'content' => '<p>Contenido demo para '.$title.'. Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>',
                    'excerpt' => 'Resumen demo: '.$title,
                    'status' => Post::STATUS_PUBLISHED,
                    'published_at' => Carbon::now()->subDays($i),
                    'line_id' => $lines[0]->id,
                ]
            );
        }
        $this->command->info('Posts listos.');

        // ── 7. BONOS ──
        $bonusData = [
            ['code' => 'BIENVENIDA50', 'title' => 'Bono Bienvenida 50%', 'percent' => 50, 'max' => 50000],
            ['code' => 'DEPOSITO100', 'title' => 'Bono Depósito 100%', 'percent' => 100, 'max' => 100000],
            ['code' => 'RECARGA30', 'title' => 'Bono Recarga 30%', 'percent' => 30, 'max' => 30000],
            ['code' => 'VIP200', 'title' => 'Bono VIP 200%', 'percent' => 200, 'max' => 200000],
        ];
        foreach ($bonusData as $bd) {
            $bonusUser = $users[$i % count($users)] ?? $users[0];
            Bonus::firstOrCreate(
                ['code' => $bd['code']],
                [
                    'title' => $bd['title'],
                    'description' => 'Bono demo del '.$bd['percent'].'% hasta $'.number_format($bd['max']),
                    'start_date' => Carbon::now()->subDays(30),
                    'end_date' => Carbon::now()->addDays(30),
                    'bonus_percent' => $bd['percent'],
                    'max_bonus' => $bd['max'],
                    'status' => 'active',
                    'user_id' => $bonusUser->id,
                    'line_id' => $lines[0]->id,
                ]
            );
        }
        $this->command->info('Bonos listos.');

        // ── 8. BONUS ASSIGNMENTS ──
        BonusAssignment::firstOrCreate(
            ['bonus_id' => Bonus::first()->id, 'user_id' => $users[0]->id],
            [
                'status' => 'active',
                'assigned_at' => Carbon::now(),
                'expired_at' => Carbon::now()->addDays(15),
            ]
        );
        BonusAssignment::firstOrCreate(
            ['bonus_id' => Bonus::skip(1)->first()->id, 'user_id' => $users[1]->id],
            [
                'status' => 'used',
                'assigned_at' => Carbon::now()->subDays(5),
                'used_at' => Carbon::now()->subDays(2),
                'expired_at' => Carbon::now()->subDays(1),
            ]
        );
        $this->command->info('Bonus assignments listos.');

        // ── 9. VENTAS (últimos 30 días) ──
        foreach ($users as $ui => $user) {
            for ($d = 0; $d < 15; $d++) {
                $date = Carbon::now()->subDays($d);
                $qty = rand(0, 3);
                for ($s = 0; $s < $qty; $s++) {
                    Sale::firstOrCreate(
                        [
                            'line_id' => $lines[array_rand($lines)]->id,
                            'client_id' => $user->id,
                            'fecha_inicio' => $date->format('Y-m-d'),
                            'monto_fichas' => rand(500, 50000),
                        ],
                        [
                            'agent_id' => $agents[array_rand($agents)]->id,
                            'platform_id' => $platforms[array_rand($platforms)]->id,
                            'fecha_fin' => $date->format('Y-m-d'),
                            'descripcion' => 'Venta demo #'.uniqid(),
                            'ganancia_superagente' => rand(50, 5000),
                        ]
                    );
                }
            }
        }
        $this->command->info('Ventas listas.');

        // ── 10. TICKETS ──
        $ticketSubjects = [
            'Problema con el depósito',
            'Consulta sobre bonos disponibles',
            'No puedo acceder a mi cuenta',
            'Tardanza en la retirada de fondos',
            'Error al realizar una apuesta',
            'Duda sobre el código de promoción',
            'Cuenta bloqueada sin motivo',
        ];
        $tickets = [];
        foreach ($ticketSubjects as $i => $subj) {
            $t = Ticket::firstOrCreate(
                ['tracking_code' => 'TKT-DEMO'.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $users[$i % count($users)]->id,
                    'line_id' => $lines[$i % count($lines)]->id,
                    'subject' => $subj,
                    'status' => $i < 4 ? 'open' : ($i < 6 ? 'progress' : 'closed'),
                    'priority' => $i < 2 ? 'high' : 'medium',
                ]
            );
            $tickets[] = $t;

            TicketMessage::firstOrCreate(
                ['ticket_id' => $t->id, 'message' => 'Mensaje demo: '.$subj],
                [
                    'agent_id' => $agents[$i % count($agents)]->id,
                    'created_at' => Carbon::now()->subHours($i * 3),
                ]
            );
        }
        $this->command->info('Tickets listos.');

        // ── 11. SORTEOS ──
        $raffleData = [
            ['title' => 'Sorteo Semanal', 'end' => 500, 'status' => 'active', 'start' => Carbon::now()->subDays(5), 'end_date' => Carbon::now()->addDays(2)],
            ['title' => 'Sorteo Mensual VIP', 'end' => 200, 'status' => 'active', 'start' => Carbon::now()->subDays(15), 'end_date' => Carbon::now()->addDays(13)],
            ['title' => 'Sorteo Fin de Semana', 'end' => 300, 'status' => 'inactive', 'start' => Carbon::now()->addDays(5), 'end_date' => Carbon::now()->addDays(12)],
        ];
        foreach ($raffleData as $rd) {
            $r = Raffle::firstOrCreate(
                ['title' => $rd['title']],
                [
                    'status' => $rd['status'],
                    'start_date' => $rd['start'],
                    'end_date' => $rd['end_date'],
                    'end_number' => $rd['end'],
                    'start_number' => 1,
                    'line_id' => $lines[0]->id,
                    'platform_id' => $platforms[0]->id,
                    'prizes' => [['name' => 'Premio principal', 'value' => 50000]],
                ]
            );

            // Add some raffle numbers
            foreach ($users as $ui => $user) {
                if ($ui % 2 === 0 && $rd['status'] !== 'upcoming') {
                    RaffleNumber::firstOrCreate(
                        ['raffle_id' => $r->id, 'user_id' => $user->id],
                        ['number' => rand(1, $rd['end'])]
                    );
                }
            }
        }
        $this->command->info('Sorteos listos.');

        // ── 12. CAROUSEL ITEMS ──
        CarouselItem::firstOrCreate(
            ['title' => 'Bienvenido a RED PICANTES'],
            ['image' => '/storage/carousel/demo1.jpg', 'link' => route('bonos'), 'order' => 1, 'line_id' => $lines[0]->id]
        );
        CarouselItem::firstOrCreate(
            ['title' => 'Bonos exclusivos'],
            ['image' => '/storage/carousel/demo2.jpg', 'link' => route('sorteos'), 'order' => 2, 'line_id' => $lines[0]->id]
        );
        $this->command->info('Carousel items listos.');

        $this->command->info('====================================');
        $this->command->info(' DATOS DE DEMO CARGADOS');
        $this->command->info('====================================');
        $this->command->info(' Admin: admin@demo.com / demo123');
        $this->command->info(' Agente: carlos@demo.com / demo123');
        $this->command->info(' Cliente: cliente1@demo.com / demo123');
        $this->command->info('====================================');
    }
}
