<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\CarouselItem;
use App\Models\HomeConfig;
use App\Models\HomeSection;
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
use App\Models\Vendor;
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

        // ── 2. ROLES (incluye CAJERO) ──
        $roleCajero = Role::firstOrCreate(['name' => Roles::CAJERO], ['display_name' => 'Cajero']);

        // ── 3. VENDORS (4 vendors + usuarios cajero) ──
        $vendorData = [
            ['name' => 'Casino Royale', 'slug' => 'casino-royale', 'logo' => 'https://picsum.photos/id/1011/200/200', 'hero' => 'https://picsum.photos/id/1015/1200/400', 'portrait' => 'https://picsum.photos/id/1005/300/400'],
            ['name' => 'BetMaster', 'slug' => 'betmaster', 'logo' => 'https://picsum.photos/id/106/200/200', 'hero' => 'https://picsum.photos/id/160/1200/400', 'portrait' => 'https://picsum.photos/id/201/300/400'],
            ['name' => 'Lucky Spin', 'slug' => 'lucky-spin', 'logo' => 'https://picsum.photos/id/133/200/200', 'hero' => 'https://picsum.photos/id/251/1200/400', 'portrait' => 'https://picsum.photos/id/29/300/400'],
            ['name' => 'Golden Palace', 'slug' => 'golden-palace', 'logo' => 'https://picsum.photos/id/180/200/200', 'hero' => 'https://picsum.photos/id/312/1200/400', 'portrait' => 'https://picsum.photos/id/48/300/400'],
        ];
        $vendors = [];
        foreach ($vendorData as $i => $vd) {
            $cajeroUser = User::updateOrCreate(
                ['email' => 'cajero'.($i + 1).'@demo.com'],
                [
                    'name' => 'Cajero '.($i + 1),
                    'password' => bcrypt('demo123'),
                    'username' => 'cajero'.($i + 1),
                    'phone' => '+54911000000'.($i + 1),
                    'role_id' => $roleCajero->id,
                    'status' => 'active',
                ]
            );
            $vendors[] = Vendor::updateOrCreate(
                ['slug' => $vd['slug']],
                [
                    'user_id' => $cajeroUser->id,
                    'name' => $vd['name'],
                    'logo' => $vd['logo'] ?? null,
                    'hero_image' => $vd['hero'] ?? null,
                    'portrait_image' => $vd['portrait'] ?? null,
                    'is_active' => true,
                    'description' => 'Demo vendor '.$vd['name'],
                ]
            );
        }
        $this->command->info('Vendors listos.');

        // ── 3. LINEAS (asociadas a vendors) ──
        $lineData = [
            ['name' => 'VIP Casino', 'icon' => '🔴', 'type' => 'whatsapp', 'phone' => '+5491112345678', 'vendor' => 0, 'portada' => 'https://picsum.photos/id/1016/800/600', 'perfil' => 'https://picsum.photos/id/1009/400/400'],
            ['name' => 'Gold Sports', 'icon' => '🟡', 'type' => 'whatsapp', 'phone' => '+5491123456789', 'vendor' => 0, 'portada' => 'https://picsum.photos/id/160/800/600', 'perfil' => 'https://picsum.photos/id/201/400/400'],
            ['name' => 'Platinum Club', 'icon' => '🔵', 'type' => 'whatsapp', 'phone' => '+5491134567890', 'vendor' => 1, 'portada' => 'https://picsum.photos/id/251/800/600', 'perfil' => 'https://picsum.photos/id/29/400/400'],
            ['name' => 'Elite Bet', 'icon' => '🟢', 'type' => 'whatsapp', 'phone' => '+5491145678901', 'vendor' => 2, 'portada' => 'https://picsum.photos/id/312/800/600', 'perfil' => 'https://picsum.photos/id/48/400/400'],
            ['name' => 'Royal Spin', 'icon' => '🟣', 'type' => 'whatsapp', 'phone' => '+5491156789012', 'vendor' => 3, 'portada' => 'https://picsum.photos/id/133/800/600', 'perfil' => 'https://picsum.photos/id/106/400/400'],
        ];
        $lines = [];
        foreach ($lineData as $ld) {
            $lines[] = Line::updateOrCreate(
                ['name' => $ld['name']],
                [
                    'vendor_id' => $vendors[$ld['vendor']]->id,
                    'icon' => $ld['icon'],
                    'type' => $ld['type'],
                    'phone' => $ld['phone'],
                    'portada_url' => $ld['portada'] ?? null,
                    'perfil_url' => $ld['perfil'] ?? null,
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
                    'line_id' => $lines[array_rand($lines)]->id,
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
        foreach ($bonusData as $bi => $bd) {
            Bonus::withoutGlobalScopes()->firstOrCreate(
                ['code' => $bd['code']],
                [
                    'title' => $bd['title'],
                    'description' => 'Bono demo del '.$bd['percent'].'% hasta $'.number_format($bd['max']),
                    'start_date' => Carbon::now()->subDays(5),
                    'end_date' => Carbon::now()->addDays(30 - $bi),
                    'type' => 'general',
                    'bonus_percent' => $bd['percent'],
                    'bonus_amount' => 0,
                    'min_deposit' => 0,
                    'max_bonus' => $bd['max'],
                    'total_quantity' => 200,
                    'per_user_limit' => 1,
                    'status' => 'active',
                    'line_id' => $lines[$bi % count($lines)]->id,
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
                    'line_id' => $lines[array_rand($lines)]->id,
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
        $carouselData = [
            ['title' => 'Bienvenido a RED PICANTES', 'img' => 'https://picsum.photos/id/1057/1600/680', 'link' => '/lineas'],
            ['title' => 'Bonos exclusivos activos',   'img' => 'https://picsum.photos/id/1058/1600/680', 'link' => '/bonos'],
            ['title' => 'Sorteo VIP de la semana',    'img' => 'https://picsum.photos/id/1059/1600/680', 'link' => '/sorteos'],
        ];
        foreach ($carouselData as $ci => $cd) {
            $item = CarouselItem::firstOrCreate(
                ['title' => $cd['title']],
                ['image' => $cd['img'], 'link' => $cd['link'], 'order' => $ci + 1, 'line_id' => $lines[$ci % count($lines)]->id]
            );
            HomeConfig::firstOrCreate(
                ['section' => HomeConfig::SECTION_CAROUSEL, 'item_id' => $item->id],
                ['order' => $ci + 1]
            );
        }
        $this->command->info('Carousel items listos.');

        // ── 13. HOME SECTIONS ──
        $activePosts = Post::withoutGlobalScopes()->where('status', Post::STATUS_PUBLISHED)->take(6)->pluck('id')->toArray();
        $activeBonuses = Bonus::withoutGlobalScopes()->where('status', 'active')->where('end_date', '>=', now())->take(6)->pluck('id')->toArray();
        $activeRaffles = Raffle::withoutGlobalScopes()->where('status', 'active')->where('end_date', '>=', now())->pluck('id')->toArray();
        $activeLines = Line::withoutGlobalScopes()->where('status', 'active')->take(6)->pluck('id')->toArray();

        $homeSections = [
            ['section_key' => 'como-empezar', 'kicker' => 'Cómo funciona', 'title' => 'Empezá en', 'highlight' => '3 pasos', 'subtitle' => 'Sin vueltas: contacto, carga y juego. Si necesitás ayuda, una persona te responde.', 'repeater_data' => [['title' => 'Pedí tu usuario', 'subtitle' => 'Elegí una línea de atención y solicitá el acceso.'], ['title' => 'Cargá saldo', 'subtitle' => 'Consultá los medios de pago y acreditamos al instante.'], ['title' => 'Jugá', 'subtitle' => 'Entrá a la plataforma y disfrutá del casino online.']]],
            ['section_key' => 'lineas', 'kicker' => 'Empezá a jugar', 'title' => 'Líneas de', 'highlight' => 'atención', 'subtitle' => 'Hablá con una línea, pedí tu usuario, cargá saldo y entrá al casino en minutos.', 'line_ids' => $activeLines],
            ['section_key' => 'sorteo', 'kicker' => 'Muy pronto', 'title' => 'PRÓXIMOS', 'highlight' => 'SORTEOS', 'subtitle' => 'Nuevas oportunidades para ganar. Registrate y enterate antes que nadie.', 'raffle_ids' => $activeRaffles],
            ['section_key' => 'nosotros', 'kicker' => 'Sobre nosotros', 'title' => 'Casino online con atención', 'highlight' => 'real', 'subtitle' => 'Una experiencia pensada para jugar fácil: acceso rápido, bonos claros, sorteos activos y soporte humano.', 'repeater_data' => [['title' => 'Alta rápida', 'subtitle' => 'Pedís tu usuario sin formularios eternos.'], ['title' => 'Bonos vigentes', 'subtitle' => 'Bonos para recargar y jugar más.'], ['title' => 'Sorteos activos', 'subtitle' => 'Premios y chances extra para los participantes.'], ['title' => 'Soporte humano', 'subtitle' => 'Atención directa para cargas, retiros y dudas.']]],
            ['section_key' => 'bonos', 'kicker' => 'Bonos para jugar más', 'title' => 'Bonos', 'highlight' => 'activos', 'subtitle' => 'Bonos vigentes para arrancar mejor y aprovechar cada jugada.', 'action_text' => 'Ver todos', 'action_url' => '/bonos', 'bonus_ids' => $activeBonuses],
            ['section_key' => 'blog', 'kicker' => 'Noticias y jugadas', 'title' => 'Noticias y', 'highlight' => 'novedades', 'subtitle' => 'Enterate de sorteos, bonos y novedades antes que nadie.', 'action_text' => 'Ver novedades', 'action_url' => '/blog', 'post_ids' => $activePosts],
        ];

        foreach ($homeSections as $order => $data) {
            HomeSection::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => null, 'section_key' => $data['section_key']],
                array_merge($data, [
                    'vendor_id' => null,
                    'enabled' => true,
                    'order' => $order,
                ])
            );
        }
        $this->command->info('HomeSections globales listas.');

        $this->command->info('====================================');
        $this->command->info(' DATOS DE DEMO CARGADOS');
        $this->command->info('====================================');
        $this->command->info(' Admin: admin@demo.com / demo123');
        $this->command->info(' Agente: carlos@demo.com / demo123');
        $this->command->info(' Cliente: cliente1@demo.com / demo123');
        $this->command->info('====================================');
    }
}
