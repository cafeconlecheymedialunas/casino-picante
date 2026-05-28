<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\CarouselItem;
use App\Models\Category;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Comment;
use App\Models\DashboardNotification;
use App\Models\HomeConfig;
use App\Models\HomeSection;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\LineRating;
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
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::withoutGlobalScopes()->exists()) {
            $this->command->info('Base con datos existentes — corriendo solo seeders complementarios.');
            $this->call([
                DirectVendorSeeder::class,
                HomeSectionSeeder::class,
                RaffleDemoSeeder::class,
            ]);

            return;
        }

        $roles = $this->seedRoles();

        $admin = User::create([
            'role_id' => $roles[Roles::ADMIN]->id,
            'username' => 'admin',
            'name' => 'Admin',
            'apellido' => 'General',
            'email' => 'admin@redpicantes.test',
            'password' => Hash::make('password'),
            'phone' => '+54 9 11 4000-1000',
            'status' => 'active',
            'avatar' => $this->avatar('Admin General'),
        ]);

        $adminAgent = Agent::create([
            'user_id' => $admin->id,
            'username' => 'admin_red',
            'name' => 'Admin',
            'apellido' => 'General',
            'email' => $admin->email,
            'password' => Hash::make('password'),
            'phone' => $admin->phone,
            'status' => 'active',
            'cargo' => 'Administrador general',
            'avatar' => $admin->avatar,
        ]);

        $agents = collect([
            $this->createAgent($roles[Roles::AGENTE], 'Sofia', 'Paz', 'sofiapaz', 'sofia@redpicantes.test', '+54 9 11 5100-2201', 'Super agente'),
            $this->createAgent($roles[Roles::AGENTE], 'Bruno', 'Rivas', 'brunorivas', 'bruno@redpicantes.test', '+54 9 11 5100-2202', 'Atencion VIP'),
            $this->createAgent($roles[Roles::AGENTE], 'Micaela', 'Luna', 'micaluna', 'mica@redpicantes.test', '+54 9 11 5100-2203', 'Cargas y retiros'),
            $this->createAgent($roles[Roles::AGENTE], 'Nicolas', 'Vega', 'nicovega', 'nico@redpicantes.test', '+54 9 11 5100-2204', 'Soporte jugadores'),
        ])->prepend($adminAgent)->values();

        $platforms = $this->seedPlatforms();
        $lines = $this->seedLines($agents, $platforms);
        $clients = $this->seedClients($roles[Roles::CLIENTE], $lines);
        $categories = $this->seedCategories();
        $posts = $this->seedPosts($categories, $lines, $clients, $agents);
        $bonuses = $this->seedBonuses($lines, $platforms, $adminAgent, $clients);
        $raffles = $this->seedRaffles($lines, $platforms, $clients);
        $this->seedCarousel($lines);
        $this->seedHomeConfig($posts, $bonuses);
        $this->seedHomeSections($posts, $bonuses, $lines);
        $vendors = $this->seedVendors($roles[Roles::CAJERO], $platforms, $agents, $clients, $bonuses, $raffles, $posts);
        $this->assignDemoClientsToVendors($clients, $vendors);
        $this->assignDemoAgentsToVendors($agents, $vendors);
        $this->seedVendorHomeSections($vendors, $bonuses, $raffles, $posts);
        $this->seedSupportData($lines, $agents, $clients, $posts, $platforms);

        $this->command->info('Base demo cargada.');
        $this->command->info('Admin: admin@redpicantes.test / password');
        $this->command->info('Agente demo: sofia@redpicantes.test / password');
        $this->command->info('Cliente demo: valentina@demo.test / password');
    }

    private function seedRoles(): array
    {
        $roles = [];

        foreach ([
            Roles::ADMIN => 'Admin',
            Roles::AGENTE => 'Agente',
            Roles::CLIENTE => 'Cliente',
            Roles::CAJERO => 'Cajero',
        ] as $name => $label) {
            $roles[$name] = Role::updateOrCreate(['name' => $name], ['label' => $label]);
        }

        return $roles;
    }

    private function createAgent(Role $role, string $name, string $lastName, string $username, string $email, string $phone, string $cargo): Agent
    {
        $user = User::create([
            'role_id' => $role->id,
            'username' => $username,
            'name' => $name,
            'apellido' => $lastName,
            'email' => $email,
            'password' => Hash::make('password'),
            'phone' => $phone,
            'status' => 'active',
            'avatar' => $this->avatar($name.' '.$lastName),
        ]);

        return Agent::create([
            'user_id' => $user->id,
            'username' => $username,
            'name' => $name,
            'apellido' => $lastName,
            'email' => $email,
            'password' => Hash::make('password'),
            'phone' => $phone,
            'status' => 'active',
            'cargo' => $cargo,
            'avatar' => $user->avatar,
        ]);
    }

    private function seedPlatforms(): Collection
    {
        return collect([
            ['name' => 'VIP Casino',   'slug' => 'vip-casino',   'img' => '1051075', 'description' => 'Slots, ruleta en vivo y mesas premium con carga rapida.',              'contacts' => [['name' => 'Soporte VIP',     'type' => 'whatsapp', 'value' => 'https://wa.me/5491151003301']]],
            ['name' => 'Hybrid Club',  'slug' => 'hybrid-club',  'img' => '1047887', 'description' => 'Casino online con torneos diarios y juegos en vivo.',                  'contacts' => [['name' => 'Alta Hybrid',     'type' => 'telegram', 'value' => 'https://t.me/redpicantes']]],
            ['name' => 'Etoile Play',  'slug' => 'etoile-play',  'img' => '1055680', 'description' => 'Experiencia simple para cargar, jugar y retirar sin vueltas.',         'contacts' => [['name' => 'Mesa Etoile',     'type' => 'instagram', 'value' => 'https://instagram.com/redpicantes']]],
            ['name' => 'Golden Bet',   'slug' => 'golden-bet',   'img' => '1068523', 'description' => 'Bonos de recarga, ruleta y premios semanales.',                        'contacts' => [['name' => 'Golden Atencion', 'type' => 'web',      'value' => 'https://redpicantes.test']]],
        ])->map(fn ($platform) => Platform::create([
            'name' => $platform['name'],
            'slug' => $platform['slug'],
            'description' => $platform['description'],
            'contacts' => $platform['contacts'],
            'logo_url' => 'https://picsum.photos/id/'.$platform['img'].'/256/256',
            'website_url' => 'https://redpicantes.test/'.$platform['slug'],
            'is_active' => true,
        ]));
    }

    private function seedLines(Collection $agents, Collection $platforms): Collection
    {
        $lineData = [
            [
                'name' => 'Linea Fuego VIP',
                'type' => 'vip',
                'description' => 'Atencion prioritaria para altas, recargas grandes, retiros y beneficios VIP.',
                'portada_url' => 'https://picsum.photos/id/1076/1200/420',
                'perfil_url' => 'https://picsum.photos/id/1033/256/256',
                'contact_links' => [
                    ['name' => 'WhatsApp VIP', 'type' => 'whatsapp', 'value' => 'https://wa.me/5491151004401'],
                    ['name' => 'Telegram VIP', 'type' => 'telegram', 'value' => 'https://t.me/fuegovip'],
                ],
                'manager' => 1,
                'members' => [2],
                'platforms' => [0, 1, 3],
            ],
            [
                'name' => 'Linea Ruleta Pro',
                'type' => 'pro',
                'description' => 'Linea enfocada en ruleta en vivo, mesas rapidas y seguimiento de bonos.',
                'portada_url' => 'https://picsum.photos/id/1062/1200/420',
                'perfil_url' => 'https://picsum.photos/id/1074/256/256',
                'contact_links' => [
                    ['name' => 'WhatsApp Ruleta', 'type' => 'whatsapp', 'value' => 'https://wa.me/5491151004402'],
                    ['name' => 'Instagram Mesa', 'type' => 'instagram', 'value' => 'https://instagram.com/ruletapro'],
                ],
                'manager' => 2,
                'members' => [3],
                'platforms' => [0, 2],
            ],
            [
                'name' => 'Linea Slots Express',
                'type' => 'express',
                'description' => 'Alta rapida, cargas chicas y bonos pensados para jugar slots todos los dias.',
                'portada_url' => 'https://picsum.photos/id/1080/1200/420',
                'perfil_url' => 'https://picsum.photos/id/1025/256/256',
                'contact_links' => [
                    ['name' => 'WhatsApp Slots', 'type' => 'whatsapp', 'value' => 'https://wa.me/5491151004403'],
                    ['name' => 'Canal Telegram', 'type' => 'telegram', 'value' => 'https://t.me/slotsexpress'],
                ],
                'manager' => 3,
                'members' => [4],
                'platforms' => [1, 2, 3],
            ],
            [
                'name' => 'Linea Norte',
                'type' => 'regional',
                'description' => 'Atencion regional con foco en soporte humano y pagos ordenados.',
                'portada_url' => 'https://picsum.photos/id/1060/1200/420',
                'perfil_url' => 'https://picsum.photos/id/1072/256/256',
                'contact_links' => [
                    ['name' => 'WhatsApp Norte', 'type' => 'whatsapp', 'value' => 'https://wa.me/5491151004404'],
                ],
                'manager' => 4,
                'members' => [1],
                'platforms' => [0, 3],
            ],
        ];

        return collect($lineData)->map(function ($data) use ($agents, $platforms) {
            $line = Line::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'phone' => '+54 9 11 5100-44'.random_int(10, 99),
                'icon' => 'fa-solid fa-fire',
                'description' => $data['description'],
                'status' => 'active',
                'contact_links' => $data['contact_links'],
                'best_sales' => random_int(1800000, 5400000),
                'portada_url' => $data['portada_url'],
                'perfil_url' => $data['perfil_url'],
            ]);

            $line->platforms()->sync(collect($data['platforms'])->mapWithKeys(fn ($index) => [
                $platforms[$index]->id => [
                    'custom_message' => 'Disponible en '.$data['name'].' con alta asistida y soporte para recargas.',
                    'is_active' => true,
                ],
            ])->all());

            $manager = $agents[$data['manager']];
            LineAgent::create([
                'line_id' => $line->id,
                'agent_id' => $manager->id,
                'role' => LineRoles::ENCARGADO,
                'is_active' => true,
                'porcentaje_ganancia' => random_int(25, 40),
            ]);

            foreach (Permissions::all() as $permission) {
                LineAgentPermission::create([
                    'line_id' => $line->id,
                    'agent_id' => $manager->id,
                    'permission' => $permission,
                ]);
            }

            foreach ($data['members'] as $memberIndex) {
                $member = $agents[$memberIndex];
                LineAgent::create([
                    'line_id' => $line->id,
                    'agent_id' => $member->id,
                    'role' => LineRoles::MIEMBRO,
                    'parent_id' => $manager->id,
                    'is_active' => true,
                    'porcentaje_ganancia' => 0,
                ]);

                foreach ([Permissions::DASHBOARD_READ, Permissions::TICKET_READ, Permissions::USER_READ, Permissions::BONO_READ, Permissions::SORTEO_READ, Permissions::NEWS_READ] as $permission) {
                    LineAgentPermission::create([
                        'line_id' => $line->id,
                        'agent_id' => $member->id,
                        'permission' => $permission,
                    ]);
                }
            }

            return $line;
        });
    }

    private function seedClients(Role $role, Collection $lines): Collection
    {
        $clients = collect([
            ['Valentina', 'Rossi', 'valentina', 'valentina@demo.test', '+54 9 11 6200-1001'],
            ['Tomas', 'Medina', 'tomas', 'tomas@demo.test', '+54 9 11 6200-1002'],
            ['Camila', 'Soria', 'camila', 'camila@demo.test', '+54 9 11 6200-1003'],
            ['Lucas', 'Pereyra', 'lucas', 'lucas@demo.test', '+54 9 11 6200-1004'],
            ['Martina', 'Suarez', 'martina', 'martina@demo.test', '+54 9 11 6200-1005'],
            ['Diego', 'Correa', 'diego', 'diego@demo.test', '+54 9 11 6200-1006'],
        ])->map(function ($client, $index) use ($role, $lines) {
            $user = User::create([
                'role_id' => $role->id,
                'username' => $client[2],
                'name' => $client[0],
                'apellido' => $client[1],
                'email' => $client[3],
                'password' => Hash::make('password'),
                'phone' => $client[4],
                'contact' => 'Prefiere WhatsApp por la tarde',
                'status' => 'active',
                'line_id' => $lines[$index % $lines->count()]->id,
                'avatar' => $this->avatar($client[0].' '.$client[1]),
            ]);

            $user->lines()->syncWithoutDetaching([
                $lines[$index % $lines->count()]->id => ['is_active' => true],
            ]);

            return $user;
        });

        // Inactive demo client — vendor_id assigned later via assignDemoClientsToVendors
        $pausado = User::create([
            'role_id' => $role->id,
            'username' => 'pausado',
            'name' => 'Cliente',
            'apellido' => 'Pausado',
            'email' => 'pausado@demo.test',
            'password' => Hash::make('password'),
            'status' => 'inactive',
            'line_id' => $lines->first()?->id,
        ]);
        $clients->push($pausado);

        return $clients;
    }

    private function seedCategories(): Collection
    {
        return collect(['Sorteos', 'Bonos', 'Casino online', 'Ganadores'])->map(fn ($name) => Category::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]));
    }

    private function seedPosts(Collection $categories, Collection $lines, Collection $clients, Collection $agents): Collection
    {
        $posts = [
            ['Ganadores del sorteo semanal de Julio', 'Resumen demo: estos fueron los premios entregados y las proximas fechas de participacion.', 'Sorteos',       0, '1040'],
            ['Bono especial de fin de semana',         'Recargas con beneficio extra para jugar slots y ruleta en vivo hasta el domingo.',           'Bonos',        1, '1041'],
            ['Como pedir tu usuario y empezar a jugar', 'Guia simple para elegir linea, solicitar alta y cargar saldo con atencion real.',            'Casino online', 2, '1042'],
            ['Nueva ronda de premios VIP',             'El sorteo VIP suma premios principales para usuarios activos de la semana.',                 'Sorteos',       0, '1043'],
            ['Consejos para aprovechar tus bonos',     'Usa tus codigos a tiempo y consulta las condiciones con tu linea asignada.',                'Bonos',        1, '1044'],
            ['Historia de una carga rapida',           'Un caso demo de atencion resuelto en minutos por el equipo de RED PICANTES.',               'Ganadores',    3, '1045'],
        ];

        return collect($posts)->map(function ($post, $index) use ($categories, $lines, $clients, $agents) {
            $category = $categories->firstWhere('name', $post[2]);

            $created = Post::create([
                'title' => $post[0],
                'slug' => Str::slug($post[0]),
                'content' => '<p>'.$post[1].'</p><p>La propuesta es jugar con informacion clara, canales activos y soporte humano durante todo el proceso.</p>',
                'excerpt' => $post[1],
                'image' => 'https://picsum.photos/id/'.$post[4].'/900/560',
                'status' => Post::STATUS_PUBLISHED,
                'published_at' => now()->subDays($index + 1),
                'line_id' => $lines[$post[3]]->id,
                'category_id' => $category?->id,
                'author_agent_id' => $agents[$index % $agents->count()]->id,
            ]);

            Comment::create([
                'post_id' => $created->id,
                'user_id' => $clients[$index % $clients->count()]->id,
                'content' => 'Muy buena info, me sirvio para entender como participar.',
                'is_approved' => true,
            ]);

            return $created;
        });
    }

    private function seedBonuses(Collection $lines, Collection $platforms, Agent $adminAgent, Collection $clients): Collection
    {
        $bonusData = [
            ['BIENVENIDA50', 'Bono Bienvenida 50%', 'Bono demo del 50% para primera carga asistida.', 50, 50000, 0],
            ['DEPOSITO100', 'Bono Deposito 100%', 'Duplica tu carga inicial en plataformas seleccionadas.', 100, 100000, 1],
            ['RECARGA30', 'Bono Recarga 30%', 'Beneficio de recarga para jugar el fin de semana.', 30, 30000, 2],
            ['VIP200', 'Bono VIP 200%', 'Beneficio demo para jugadores VIP con linea activa.', 200, 200000, 0],
        ];

        return collect($bonusData)->map(function ($bonus, $index) use ($lines, $platforms, $adminAgent, $clients) {
            $created = Bonus::withoutGlobalScopes()->create([
                'code' => $bonus[0],
                'title' => $bonus[1],
                'description' => $bonus[2],
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(28 - $index),
                'type' => 'general',
                'status' => 'active',
                'created_by' => $adminAgent->id,
                'bonus_percent' => $bonus[3],
                'bonus_amount' => 0,
                'min_deposit' => 0,
                'max_bonus' => $bonus[4],
                'total_quantity' => 250,
                'per_user_limit' => 1,
                'line_id' => $lines[$bonus[5]]->id,
                'platform_id' => $platforms[$index % $platforms->count()]->id,
            ]);

            BonusAssignment::create([
                'bonus_id' => $created->id,
                'user_id' => $clients[$index % $clients->count()]->id,
                'status' => $index === 0 ? 'used' : 'active',
                'assigned_at' => now()->subDays($index + 1),
                'used_at' => $index === 0 ? now()->subHours(8) : null,
            ]);

            return $created;
        });
    }

    private function seedRaffles(Collection $lines, Collection $platforms, Collection $clients): Collection
    {
        $created = collect();

        $active = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo VIP Mayo',
            'description' => 'Ganate un viaje a Brasil, un auto y premios sorpresa por participar con tus cargas semanales.',
            'status' => 'active',
            'start_date' => now()->subDays(4),
            'end_date' => now()->addHours(34),
            'start_number' => 1,
            'end_number' => 500,
            'numbers_limit' => 500,
            'line_id' => $lines[0]->id,
            'platform_id' => $platforms[0]->id,
            'prizes' => [
                ['position' => 1, 'name' => 'Viaje a Brasil',  'amount' => 7000, 'image' => 'https://picsum.photos/id/1049/480/260'],
                ['position' => 2, 'name' => 'Moto 0km',        'amount' => 3200, 'image' => 'https://picsum.photos/id/1082/480/260'],
                ['position' => 3, 'name' => 'Notebook Pro 16', 'amount' => 1800, 'image' => 'https://picsum.photos/id/0/480/260'],
            ],
        ]);
        $active->lines()->sync([$lines[0]->id, $lines[1]->id, $lines[2]->id]);

        foreach (range(1, 18) as $number) {
            RaffleNumber::create([
                'raffle_id' => $active->id,
                'user_id' => $clients[($number - 1) % $clients->count()]->id,
                'line_id' => $lines[($number - 1) % 3]->id,
                'number' => $number,
            ]);
        }

        $finished = Raffle::withoutGlobalScopes()->create([
            'title' => 'Sorteo Express Slots',
            'description' => 'Premios entregados a usuarios activos de Slots Express.',
            'status' => 'finished',
            'start_date' => now()->subDays(30),
            'end_date' => now()->subDays(2),
            'start_number' => 1,
            'end_number' => 300,
            'numbers_limit' => 300,
            'winner_user_id' => $clients[2]->id,
            'winner_number' => 88,
            'line_id' => $lines[2]->id,
            'platform_id' => $platforms[1]->id,
            'prizes' => [
                ['position' => 1, 'name' => 'Combo tecnologia', 'amount' => 1200, 'image' => 'https://picsum.photos/id/1/480/260'],
            ],
        ]);
        $finished->lines()->sync([$lines[2]->id]);
        $created->push($active);

        return $created;
    }

    private function seedCarousel(Collection $lines): void
    {
        foreach ([
            ['1057', 'Casino online con atencion real', '/lineas'],
            ['1058', 'Bonos activos para jugar mas',    '/#bonos'],
            ['1059', 'Sorteo VIP de la semana',         '/sorteo'],
        ] as $index => $item) {
            CarouselItem::create([
                'image' => 'https://picsum.photos/id/'.$item[0].'/1600/680',
                'title' => $item[1],
                'link' => $item[2],
                'order' => $index + 1,
                'line_id' => $lines[$index % $lines->count()]->id,
            ]);
        }
    }

    private function seedHomeConfig(Collection $posts, Collection $bonuses): void
    {
        CarouselItem::orderBy('order')->get()->each(fn ($item, $index) => HomeConfig::create([
            'section' => HomeConfig::SECTION_CAROUSEL,
            'item_id' => $item->id,
            'order' => $index + 1,
        ]));

        $bonuses->take(4)->values()->each(fn ($bonus, $index) => HomeConfig::create([
            'section' => HomeConfig::SECTION_BONUSES,
            'item_id' => $bonus->id,
            'order' => $index + 1,
        ]));

        $posts->take(3)->values()->each(fn ($post, $index) => HomeConfig::create([
            'section' => HomeConfig::SECTION_BLOG,
            'item_id' => $post->id,
            'order' => $index + 1,
        ]));
    }

    private function seedHomeSections(Collection $posts, Collection $bonuses, Collection $lines): void
    {
        $raffleIds = Raffle::withoutGlobalScopes()->where('status', 'active')->pluck('id')->toArray();
        $bonusIds = $bonuses->pluck('id')->toArray();
        $postIds = $posts->pluck('id')->toArray();
        $lineIds = $lines->pluck('id')->toArray();

        $sections = [
            [
                'section_key' => 'como-empezar',
                'order' => 0,
                'enabled' => true,
                'kicker' => 'Como funciona',
                'title' => 'Empeza en',
                'highlight' => '3 pasos',
                'subtitle' => 'Sin vueltas: contacto, carga y juego. Si necesitas ayuda, una persona te responde.',
                'repeater_data' => [
                    ['title' => 'Pedi tu usuario', 'subtitle' => 'Elegi una linea de atencion y solicitá el acceso para empezar a jugar.'],
                    ['title' => 'Cargá saldo', 'subtitle' => 'Consulta medios de carga y bonos disponibles para tu cuenta.'],
                    ['title' => 'Entrate a jugar', 'subtitle' => 'Disfrutá tus juegos favoritos, participá en sorteos y pedi asistencia cuando quieras.'],
                ],
            ],
            [
                'section_key' => 'lineas',
                'order' => 1,
                'enabled' => true,
                'kicker' => 'Empeza a jugar',
                'title' => 'Lineas de',
                'highlight' => 'atencion',
                'subtitle' => 'Hablá con una linea, pedi tu usuario, cargá saldo y entrate al casino en minutos.',
                'line_ids' => $lineIds,
            ],
            [
                'section_key' => 'sorteo',
                'order' => 2,
                'enabled' => true,
                'kicker' => 'Muy pronto',
                'title' => 'PROXIMOS',
                'highlight' => 'SORTEOS',
                'subtitle' => 'Nuevas oportunidades para ganar. Registrate y enterate antes que nadie.',
                'raffle_type' => 'active',
                'raffle_ids' => $raffleIds,
            ],
            [
                'section_key' => 'nosotros',
                'order' => 3,
                'enabled' => true,
                'kicker' => 'Sobre RED PICANTES',
                'title' => 'Casino online con atencion',
                'highlight' => 'real',
                'subtitle' => 'Una experiencia pensada para jugar facil: acceso rapido, bonos claros, sorteos activos y soporte humano para acompaniarte.',
                'repeater_data' => [
                    ['title' => 'Alta rapida', 'subtitle' => 'Contactas una linea y pedes tu usuario sin formularios eternos.'],
                    ['title' => 'Bonos vigentes', 'subtitle' => 'Bonos para recargar, arrancar con ventaja y jugar mas.'],
                    ['title' => 'Sorteos activos', 'subtitle' => 'Premios y chances extra para usuarios que participan.'],
                    ['title' => 'Soporte humano', 'subtitle' => 'Atencion directa para cargas, retiros, dudas y novedades.'],
                ],
            ],
            [
                'section_key' => 'bonos',
                'order' => 4,
                'enabled' => true,
                'kicker' => 'Bonos para jugar mas',
                'title' => 'Bonos',
                'highlight' => 'activos',
                'subtitle' => 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jugada.',
                'action_text' => 'Ver todos',
                'action_url' => '/bonos',
                'bonus_type' => 'active',
                'bonus_ids' => $bonusIds,
            ],
            [
                'section_key' => 'blog',
                'order' => 5,
                'enabled' => true,
                'kicker' => 'Noticias y jugadas',
                'title' => 'Noticias y',
                'highlight' => 'jugadas',
                'subtitle' => 'Enterate de novedades, sorteos, recomendaciones y bonos nuevos antes de que pasen.',
                'action_text' => 'Ver novedades',
                'action_url' => '/blog',
                'post_type' => '',
                'post_ids' => $postIds,
            ],
        ];

        foreach ($sections as $data) {
            HomeSection::create([
                'vendor_id' => null,
                'section_key' => $data['section_key'],
                'order' => $data['order'],
                'enabled' => $data['enabled'],
                'kicker' => $data['kicker'],
                'title' => $data['title'],
                'highlight' => $data['highlight'],
                'subtitle' => $data['subtitle'],
                'content' => null,
                'action_text' => $data['action_text'] ?? null,
                'action_url' => $data['action_url'] ?? null,
                'repeater_data' => $data['repeater_data'] ?? null,
                'raffle_type' => $data['raffle_type'] ?? null,
                'raffle_ids' => $data['raffle_ids'] ?? null,
                'bonus_type' => $data['bonus_type'] ?? null,
                'bonus_ids' => $data['bonus_ids'] ?? null,
                'post_type' => $data['post_type'] ?? null,
                'post_ids' => $data['post_ids'] ?? null,
                'line_ids' => $data['line_ids'] ?? null,
            ]);
        }
    }

    private function seedVendors(
        Role $cajeroRole,
        Collection $platforms,
        Collection $agents,
        Collection $clients,
        Collection $bonuses,
        Collection $raffles,
        Collection $posts,
    ): Collection {
        $vendorData = [
            [
                'name' => 'Cajero Fuego',
                'slug' => 'cajero-fuego',
                'username' => 'cajero_fuego',
                'email' => 'fuego@cajero.test',
                'first' => 'Rodrigo',
                'last' => 'Fuentes',
                'description' => 'Cajero con atención rápida, cargas en minutos y líneas activas las 24hs. Operamos VIP Casino y Golden Bet.',
                'contacts' => [
                    ['name' => 'WhatsApp', 'type' => 'whatsapp', 'value' => '5491151110001'],
                    ['name' => 'Telegram',  'type' => 'telegram',  'value' => 'https://t.me/cajerofuego'],
                    ['name' => 'Instagram', 'type' => 'instagram', 'value' => 'https://instagram.com/cajerofuego'],
                ],
                'features' => [
                    ['icon' => 'fa-solid fa-bolt',        'title' => 'Carga rápida',      'desc' => 'Saldo acreditado en menos de 5 minutos.'],
                    ['icon' => 'fa-solid fa-clock',       'title' => '24/7',              'desc' => 'Atención disponible todos los días.'],
                    ['icon' => 'fa-solid fa-shield-halved', 'title' => 'Confiable',        'desc' => 'Operaciones seguras y verificadas.'],
                    ['icon' => 'fa-solid fa-gift',        'title' => 'Bonos exclusivos',  'desc' => 'Bonos propios para usuarios de esta línea.'],
                ],
                'platform_indices' => [0, 3],
                'logo_id' => '1072',
                'hero_id' => '1076',
                'portrait_id' => '1062',
                'line_img_ids' => ['10', '11'],
                'prize_img_ids' => ['1049', '1050'],
            ],
            [
                'name' => 'Cajero Luna',
                'slug' => 'cajero-luna',
                'username' => 'cajero_luna',
                'email' => 'luna@cajero.test',
                'first' => 'Valentina',
                'last' => 'Cruz',
                'description' => 'Especializada en Hybrid Club y Etoile Play. Atención personalizada, bonos de bienvenida y sorteos semanales.',
                'contacts' => [
                    ['name' => 'WhatsApp', 'type' => 'whatsapp', 'value' => '5491151110002'],
                    ['name' => 'Telegram',  'type' => 'telegram',  'value' => 'https://t.me/cajeroluna'],
                ],
                'features' => [
                    ['icon' => 'fa-solid fa-star',        'title' => 'Trato VIP',         'desc' => 'Atención personalizada para cada jugador.'],
                    ['icon' => 'fa-solid fa-trophy',      'title' => 'Sorteos activos',   'desc' => 'Participá en sorteos semanales y ganá premios.'],
                    ['icon' => 'fa-solid fa-percent',     'title' => 'Bonos reales',      'desc' => 'Sin letra chica: los bonos aplican al instante.'],
                    ['icon' => 'fa-solid fa-headset',     'title' => 'Soporte humano',    'desc' => 'Una persona real te atiende, no un bot.'],
                ],
                'platform_indices' => [1, 2],
                'logo_id' => '1074',
                'hero_id' => '1080',
                'portrait_id' => '1025',
                'line_img_ids' => ['15', '16'],
                'prize_img_ids' => ['1041', '1042'],
            ],
            [
                'name' => 'Cajero Norte',
                'slug' => 'cajero-norte',
                'username' => 'cajero_norte',
                'email' => 'norte@cajero.test',
                'first' => 'Matias',
                'last' => 'Olmedo',
                'description' => 'Cajero regional con líneas en todas las plataformas. Pagos ordenados, retiros rápidos y seguimiento de cada operación.',
                'contacts' => [
                    ['name' => 'WhatsApp', 'type' => 'whatsapp', 'value' => '5491151110003'],
                    ['name' => 'Email',     'type' => 'email',    'value' => 'norte@cajero.test'],
                ],
                'features' => [
                    ['icon' => 'fa-solid fa-map-location-dot', 'title' => 'Regional',    'desc' => 'Presencia en todo el norte del país.'],
                    ['icon' => 'fa-solid fa-rotate',           'title' => 'Retiros ágiles', 'desc' => 'Procesamos retiros el mismo día.'],
                    ['icon' => 'fa-solid fa-file-invoice',     'title' => 'Seguimiento',  'desc' => 'Historial de cada operación disponible.'],
                    ['icon' => 'fa-solid fa-users',            'title' => 'Multi-línea',  'desc' => 'Gestión de líneas en todas las plataformas.'],
                ],
                'platform_indices' => [0, 1, 2, 3],
                'logo_id' => '1033',
                'hero_id' => '1060',
                'portrait_id' => '1073',
                'line_img_ids' => ['20', '21'],
                'prize_img_ids' => ['1043', '1044'],
            ],
        ];

        return collect($vendorData)->map(function ($data, $index) use ($cajeroRole, $platforms, $agents, $clients) {
            $user = User::create([
                'role_id' => $cajeroRole->id,
                'username' => $data['username'],
                'name' => $data['first'],
                'apellido' => $data['last'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'phone' => '+54 9 11 5111-000'.($index + 1),
                'status' => 'active',
                'avatar' => $this->avatar($data['first'].' '.$data['last']),
            ]);

            $vendor = Vendor::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'slug' => $data['slug'],
                'logo' => 'https://picsum.photos/id/'.$data['logo_id'].'/256/256',
                'hero_image' => 'https://picsum.photos/id/'.$data['hero_id'].'/1400/500',
                'portrait_image' => 'https://picsum.photos/id/'.$data['portrait_id'].'/600/800',
                'description' => $data['description'],
                'contacts' => $data['contacts'],
                'features' => $data['features'],
                'is_active' => true,
            ]);

            $user->forceFill(['vendor_id' => $vendor->id])->save();

            // Lines owned by this vendor
            $vendorLines = collect();
            foreach (range(1, 2) as $lineNum) {
                $line = Line::create([
                    'vendor_id' => $vendor->id,
                    'name' => 'Línea '.($lineNum === 1 ? 'Principal' : 'Secundaria'),
                    'type' => $lineNum === 1 ? 'vip' : 'express',
                    'phone' => '+54 9 11 5111-0'.($index * 10 + $lineNum),
                    'icon' => 'fa-solid fa-fire',
                    'description' => 'Línea de atención directa del cajero '.$data['name'].'. Cargas, retiros y soporte personalizado.',
                    'status' => 'active',
                    'contact_links' => [
                        ['name' => 'WhatsApp', 'type' => 'whatsapp', 'value' => $data['contacts'][0]['value']],
                    ],
                    'best_sales' => random_int(500000, 3000000),
                    'portada_url' => 'https://picsum.photos/id/'.($data['line_img_ids'][$lineNum - 1] ?? '1060').'/1200/420',
                    'perfil_url' => 'https://picsum.photos/id/'.($data['line_img_ids'][$lineNum - 1] ?? '1060').'/256/256',
                ]);

                $line->platforms()->sync(
                    collect($data['platform_indices'])->mapWithKeys(fn ($pi) => [
                        $platforms[$pi]->id => [
                            'custom_message' => 'Alta asistida por '.$data['name'].'. Soporte directo.',
                            'is_active' => true,
                        ],
                    ])->all()
                );

                $agentForLine = $agents[$index % $agents->count()];
                LineAgent::create([
                    'vendor_id' => $vendor->id,
                    'line_id' => $line->id,
                    'agent_id' => $agentForLine->id,
                    'role' => LineRoles::ENCARGADO,
                    'is_active' => true,
                    'porcentaje_ganancia' => 30,
                ]);
                foreach (Permissions::all() as $perm) {
                    LineAgentPermission::create([
                        'vendor_id' => $vendor->id,
                        'line_id' => $line->id,
                        'agent_id' => $agentForLine->id,
                        'permission' => $perm,
                    ]);
                }

                LineRating::create([
                    'vendor_id' => $vendor->id,
                    'line_id' => $line->id,
                    'user_id' => $clients[$index % $clients->count()]->id,
                    'rating' => 5,
                    'message' => 'Excelente atención del cajero, muy rápido y confiable.',
                ]);

                $vendorLines->push($line);
            }

            // Bonuses for this vendor
            $vendorBonuses = collect();
            foreach ([
                ['BIENVENIDA-'.strtoupper($data['slug']), 'Bono Bienvenida '.$data['name'], 'Primer bono exclusivo para usuarios de '.$data['name'].'.', 50, 50000],
                ['RECARGA-'.strtoupper($data['slug']),    'Bono Recarga '.$data['name'],     'Recarga con ventaja en '.$data['name'].'.', 30, 30000],
            ] as $bi => $bdata) {
                $bonus = Bonus::withoutGlobalScopes()->create([
                    'code' => $bdata[0],
                    'title' => $bdata[1],
                    'description' => $bdata[2],
                    'start_date' => now()->subDays(2),
                    'end_date' => now()->addDays(20),
                    'type' => 'general',
                    'status' => 'active',
                    'created_by' => $agents[0]->id,
                    'bonus_percent' => $bdata[3],
                    'bonus_amount' => 0,
                    'min_deposit' => 0,
                    'max_bonus' => $bdata[4],
                    'total_quantity' => 100,
                    'per_user_limit' => 1,
                    'vendor_id' => $vendor->id,
                    'line_id' => $vendorLines->first()->id,
                    'platform_id' => $platforms[$data['platform_indices'][0]]->id,
                ]);
                $vendorBonuses->push($bonus);
            }

            // Raffle for this vendor
            $raffle = Raffle::withoutGlobalScopes()->create([
                'vendor_id' => $vendor->id,
                'title' => 'Sorteo '.$data['name'],
                'description' => 'Sorteo exclusivo para usuarios activos de '.$data['name'].'. Participá con tus cargas semanales.',
                'status' => 'active',
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(14),
                'start_number' => 1,
                'end_number' => 200,
                'numbers_limit' => 200,
                'line_id' => $vendorLines->first()->id,
                'platform_id' => $platforms[$data['platform_indices'][0]]->id,
                'prizes' => [
                    ['position' => 1, 'name' => 'Premio Principal',  'amount' => 3000, 'image' => 'https://picsum.photos/id/'.($data['prize_img_ids'][0] ?? '1049').'/480/260'],
                    ['position' => 2, 'name' => 'Premio Secundario', 'amount' => 1000, 'image' => 'https://picsum.photos/id/'.($data['prize_img_ids'][1] ?? '1050').'/480/260'],
                ],
            ]);
            $raffle->lines()->sync($vendorLines->pluck('id')->toArray());

            foreach (range(1, 8) as $n) {
                RaffleNumber::create([
                    'vendor_id' => $vendor->id,
                    'raffle_id' => $raffle->id,
                    'user_id' => $clients[($n - 1) % $clients->count()]->id,
                    'line_id' => $vendorLines->first()->id,
                    'number' => $n,
                ]);
            }

            return $vendor;
        });
    }

    private function seedVendorHomeSections(
        Collection $vendors,
        Collection $bonuses,
        Collection $raffles,
        Collection $posts,
    ): void {
        $postIds = $posts->pluck('id')->toArray();

        foreach ($vendors as $vendor) {
            $vendorBonusIds = Bonus::withoutGlobalScopes()
                ->where('vendor_id', $vendor->id)->pluck('id')->toArray();
            $vendorRaffleIds = Raffle::withoutGlobalScopes()
                ->where('vendor_id', $vendor->id)->pluck('id')->toArray();
            $vendorLineIds = Line::withoutGlobalScopes()
                ->where('vendor_id', $vendor->id)->where('status', 'active')->pluck('id')->toArray();

            $sections = [
                [
                    'section_key' => 'como-empezar',
                    'order' => 0,
                    'enabled' => true,
                    'kicker' => 'Cómo funciona',
                    'title' => 'Empezá con',
                    'highlight' => $vendor->name,
                    'subtitle' => 'Contactanos, pedí tu usuario y empezá a jugar en minutos. Atención directa, sin vueltas.',
                    'repeater_data' => [
                        ['title' => 'Contactanos', 'subtitle' => 'Escribinos por WhatsApp o Telegram y pedí tu acceso.'],
                        ['title' => 'Cargá saldo', 'subtitle' => 'Te indicamos los medios de pago disponibles y acreditamos al instante.'],
                        ['title' => 'Jugá', 'subtitle' => 'Entrá a la plataforma con tu usuario y disfrutá del casino online.'],
                    ],
                ],
                [
                    'section_key' => 'lineas',
                    'order' => 1,
                    'enabled' => ! empty($vendorLineIds),
                    'kicker' => 'Líneas activas',
                    'title' => 'Nuestras',
                    'highlight' => 'líneas',
                    'subtitle' => 'Líneas de atención propias con soporte directo, cargas rápidas y seguimiento de cada operación.',
                    'line_ids' => $vendorLineIds,
                ],
                [
                    'section_key' => 'sorteo',
                    'order' => 2,
                    'enabled' => ! empty($vendorRaffleIds),
                    'kicker' => 'Sorteos exclusivos',
                    'title' => 'Sorteos de',
                    'highlight' => $vendor->name,
                    'subtitle' => 'Participá en nuestros sorteos exclusivos y ganá premios reales.',
                    'raffle_type' => 'active',
                    'raffle_ids' => $vendorRaffleIds,
                ],
                [
                    'section_key' => 'nosotros',
                    'order' => 3,
                    'enabled' => true,
                    'kicker' => 'Sobre '.$vendor->name,
                    'title' => 'Por qué elegirnos',
                    'highlight' => '',
                    'subtitle' => $vendor->description,
                    'repeater_data' => $vendor->features ?? [],
                ],
                [
                    'section_key' => 'bonos',
                    'order' => 4,
                    'enabled' => ! empty($vendorBonusIds),
                    'kicker' => 'Bonos exclusivos',
                    'title' => 'Bonos de',
                    'highlight' => $vendor->name,
                    'subtitle' => 'Bonos activos para arrancar con ventaja y recargar con beneficio extra.',
                    'action_text' => 'Ver todos',
                    'action_url' => '/'.$vendor->slug.'/bonos',
                    'bonus_type' => 'active',
                    'bonus_ids' => $vendorBonusIds,
                ],
                [
                    'section_key' => 'blog',
                    'order' => 5,
                    'enabled' => true,
                    'kicker' => 'Novedades',
                    'title' => 'Últimas',
                    'highlight' => 'novedades',
                    'subtitle' => 'Enterate de sorteos, bonos y noticias del casino.',
                    'action_text' => 'Ver novedades',
                    'action_url' => '/'.$vendor->slug.'/blog',
                    'post_type' => '',
                    'post_ids' => $postIds,
                ],
            ];

            foreach ($sections as $data) {
                HomeSection::create([
                    'vendor_id' => $vendor->id,
                    'section_key' => $data['section_key'],
                    'order' => $data['order'],
                    'enabled' => $data['enabled'],
                    'kicker' => $data['kicker'],
                    'title' => $data['title'],
                    'highlight' => $data['highlight'],
                    'subtitle' => $data['subtitle'] ?? null,
                    'content' => null,
                    'action_text' => $data['action_text'] ?? null,
                    'action_url' => $data['action_url'] ?? null,
                    'repeater_data' => $data['repeater_data'] ?? null,
                    'raffle_type' => $data['raffle_type'] ?? null,
                    'raffle_ids' => $data['raffle_ids'] ?? null,
                    'bonus_type' => $data['bonus_type'] ?? null,
                    'bonus_ids' => $data['bonus_ids'] ?? null,
                    'post_type' => $data['post_type'] ?? null,
                    'post_ids' => $data['post_ids'] ?? null,
                    'line_ids' => $data['line_ids'] ?? null,
                ]);
            }
        }
    }

    private function assignDemoAgentsToVendors(Collection $agents, Collection $vendors): void
    {
        if ($agents->isEmpty() || $vendors->isEmpty()) {
            return;
        }

        // Resolve vendor_id for each agent from their LineAgent assignments
        $agents->each(function (Agent $agent) use ($vendors): void {
            $vendorId = LineAgent::where('agent_id', $agent->id)
                ->whereNotNull('vendor_id')
                ->value('vendor_id');

            // Agents without a line assignment get the first vendor
            if (! $vendorId) {
                $vendorId = $vendors->first()?->id;
            }

            if (! $vendorId) {
                return;
            }

            $agent->forceFill(['vendor_id' => $vendorId])->save();

            if ($agent->user_id) {
                User::withoutGlobalScopes()
                    ->whereKey($agent->user_id)
                    ->update(['vendor_id' => $vendorId]);
            }
        });
    }

    private function assignDemoClientsToVendors(Collection $clients, Collection $vendors): void
    {
        if ($clients->isEmpty() || $vendors->isEmpty()) {
            return;
        }

        $vendors = $vendors->values();

        $clients->values()->each(function (User $client, int $index) use ($vendors): void {
            $vendor = $vendors[$index % $vendors->count()];
            $line = Line::withoutGlobalScopes()
                ->where('vendor_id', $vendor->id)
                ->where('status', 'active')
                ->orderBy('id')
                ->first();

            if (! $line) {
                return;
            }

            $client->forceFill([
                'vendor_id' => $vendor->id,
                'line_id' => $line->id,
            ])->save();

            $client->lines()->sync([
                $line->id => [
                    'vendor_id' => $vendor->id,
                    'is_active' => $client->status === 'active',
                ],
            ]);
        });
    }

    private function seedSupportData(Collection $lines, Collection $agents, Collection $clients, Collection $posts, Collection $platforms): void
    {
        foreach (range(0, 11) as $index) {
            Sale::create([
                'line_id' => $lines[$index % $lines->count()]->id,
                'agent_id' => $agents[($index % ($agents->count() - 1)) + 1]->id,
                'client_id' => $clients[$index % $clients->count()]->id,
                'platform_id' => $platforms[$index % $platforms->count()]->id,
                'fecha_inicio' => now()->subDays(18 - $index),
                'fecha_fin' => now()->subDays(18 - $index),
                'descripcion' => 'Carga demo y movimiento operativo de cliente.',
                'monto_fichas' => random_int(15000, 220000),
                'ganancia_superagente' => random_int(2500, 42000),
            ]);
        }

        $ticket = Ticket::withoutGlobalScopes()->create([
            'user_id' => $clients[0]->id,
            'line_id' => $lines[0]->id,
            'subject' => 'Consulta por retiro pendiente',
            'status' => 'progress',
            'priority' => 'high',
        ]);

        TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $clients[0]->id, 'message' => 'Hola, quiero consultar por mi retiro de anoche.']);
        TicketMessage::create(['ticket_id' => $ticket->id, 'agent_id' => $agents[1]->id, 'message' => 'Ya lo estamos revisando con la linea. Te avisamos por WhatsApp.']);

        $chat = Chat::create([
            'user_id' => $clients[1]->id,
            'agent_id' => $agents[2]->id,
            'subject' => 'Alta en VIP Casino',
            'status' => 'open',
            'context_type' => 'line',
            'context_name' => $lines[1]->name,
            'context_email' => $clients[1]->email,
            'context_phone' => $clients[1]->phone,
            'context_label' => 'Alta rapida',
        ]);

        ChatMessage::create(['chat_id' => $chat->id, 'user_id' => $clients[1]->id, 'message' => 'Quiero crear usuario para jugar ruleta.']);
        ChatMessage::create(['chat_id' => $chat->id, 'agent_id' => $agents[2]->id, 'message' => 'Perfecto, te paso las plataformas disponibles.']);

        DashboardNotification::create([
            'agent_id' => $agents[1]->id,
            'title' => 'Nuevo ticket prioritario',
            'message' => 'Valentina consulto por un retiro pendiente.',
            'type' => 'warning',
            'link' => '/admin/tickets',
            'module' => 'tickets',
        ]);

        Comment::create([
            'post_id' => $posts[0]->id,
            'user_id' => $clients[2]->id,
            'content' => 'Excelente sorteo, los premios se ven muy buenos.',
            'is_approved' => true,
        ]);

        foreach ($lines as $index => $line) {
            LineRating::create([
                'line_id' => $line->id,
                'user_id' => $clients[$index % $clients->count()]->id,
                'rating' => 5,
                'message' => 'La atencion fue rapida y me explicaron bien las plataformas disponibles.',
            ]);
        }
    }

    private function avatar(string $name, string $background = 'ff6a1a', string $color = 'ffffff'): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&background='.$background.'&color='.$color.'&bold=true&size=256';
    }

    private function image(string $seed, int $width, int $height): string
    {
        return 'https://picsum.photos/seed/rp-'.$seed.'/'.$width.'/'.$height;
    }
}
