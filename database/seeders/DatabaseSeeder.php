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
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\LineRating;
use App\Models\Platform;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
        $posts = $this->seedPosts($categories, $lines, $clients);
        $bonuses = $this->seedBonuses($lines, $platforms, $adminAgent, $clients);
        $this->seedRaffles($lines, $platforms, $clients);
        $this->seedPromotions($lines, $platforms);
        $this->seedCarousel($lines);
        $this->seedHomeConfig($posts, $bonuses);
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

    private function seedPlatforms(): \Illuminate\Support\Collection
    {
        return collect([
            ['name' => 'VIP Casino', 'slug' => 'vip-casino', 'description' => 'Slots, ruleta en vivo y mesas premium con carga rapida.', 'contacts' => [['name' => 'Soporte VIP', 'type' => 'whatsapp', 'value' => 'https://wa.me/5491151003301']]],
            ['name' => 'Hybrid Club', 'slug' => 'hybrid-club', 'description' => 'Casino online con torneos diarios y juegos en vivo.', 'contacts' => [['name' => 'Alta Hybrid', 'type' => 'telegram', 'value' => 'https://t.me/redpicantes']]],
            ['name' => 'Etoile Play', 'slug' => 'etoile-play', 'description' => 'Experiencia simple para cargar, jugar y retirar sin vueltas.', 'contacts' => [['name' => 'Mesa Etoile', 'type' => 'instagram', 'value' => 'https://instagram.com/redpicantes']]],
            ['name' => 'Golden Bet', 'slug' => 'golden-bet', 'description' => 'Promos de recarga, ruleta y premios semanales.', 'contacts' => [['name' => 'Golden Atencion', 'type' => 'web', 'value' => 'https://redpicantes.test']]],
        ])->map(fn ($platform) => Platform::create([
            ...$platform,
            'logo_url' => $this->avatar($platform['name'], 'ff6a1a', '120909'),
            'website_url' => 'https://redpicantes.test/'.$platform['slug'],
            'is_active' => true,
        ]));
    }

    private function seedLines(\Illuminate\Support\Collection $agents, \Illuminate\Support\Collection $platforms): \Illuminate\Support\Collection
    {
        $lineData = [
            [
                'name' => 'Linea Fuego VIP',
                'type' => 'vip',
                'description' => 'Atencion prioritaria para altas, recargas grandes, retiros y beneficios VIP.',
                'portada_url' => $this->image('casino-vip-cover', 1200, 420),
                'perfil_url' => $this->avatar('Fuego VIP', 'ff6a1a', '160604'),
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
                'description' => 'Linea enfocada en ruleta en vivo, mesas rapidas y seguimiento de promociones.',
                'portada_url' => $this->image('roulette-pro-cover', 1200, 420),
                'perfil_url' => $this->avatar('Ruleta Pro', 'ff8a3d', '120909'),
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
                'description' => 'Alta rapida, cargas chicas y promos pensadas para jugar slots todos los dias.',
                'portada_url' => $this->image('slots-express-cover', 1200, 420),
                'perfil_url' => $this->avatar('Slots Express', 'ffb347', '180b08'),
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
                'portada_url' => $this->image('linea-norte-cover', 1200, 420),
                'perfil_url' => $this->avatar('Linea Norte', 'e6580f', '0a0606'),
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

    private function seedClients(Role $role, \Illuminate\Support\Collection $lines): \Illuminate\Support\Collection
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

        User::create([
            'role_id' => $role->id,
            'username' => 'pausado',
            'name' => 'Cliente',
            'apellido' => 'Pausado',
            'email' => 'pausado@demo.test',
            'password' => Hash::make('password'),
            'status' => 'inactive',
        ]);

        return $clients;
    }

    private function seedCategories(): \Illuminate\Support\Collection
    {
        return collect(['Sorteos', 'Promos', 'Casino online', 'Ganadores'])->map(fn ($name) => Category::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]));
    }

    private function seedPosts(\Illuminate\Support\Collection $categories, \Illuminate\Support\Collection $lines, \Illuminate\Support\Collection $clients): \Illuminate\Support\Collection
    {
        $posts = [
            ['Ganadores del sorteo semanal de Julio', 'Resumen demo: estos fueron los premios entregados y las proximas fechas de participacion.', 'Sorteos', 0, 'post-sorteo-julio'],
            ['Promocion especial de fin de semana', 'Recargas con beneficio extra para jugar slots y ruleta en vivo hasta el domingo.', 'Promos', 1, 'post-promo-finde'],
            ['Como pedir tu usuario y empezar a jugar', 'Guia simple para elegir linea, solicitar alta y cargar saldo con atencion real.', 'Casino online', 2, 'post-como-empezar'],
            ['Nueva ronda de premios VIP', 'El sorteo VIP suma premios principales para usuarios activos de la semana.', 'Sorteos', 0, 'post-premios-vip'],
            ['Consejos para aprovechar tus bonos', 'Usa tus codigos a tiempo y consulta las condiciones con tu linea asignada.', 'Promos', 1, 'post-bonos'],
            ['Historia de una carga rapida', 'Un caso demo de atencion resuelto en minutos por el equipo de RED PICANTES.', 'Ganadores', 3, 'post-carga-rapida'],
        ];

        return collect($posts)->map(function ($post, $index) use ($categories, $lines, $clients) {
            $category = $categories->firstWhere('name', $post[2]);

            $created = Post::create([
                'title' => $post[0],
                'slug' => Str::slug($post[0]),
                'content' => '<p>'.$post[1].'</p><p>La propuesta es jugar con informacion clara, canales activos y soporte humano durante todo el proceso.</p>',
                'excerpt' => $post[1],
                'image' => $this->image($post[4], 900, 560),
                'status' => Post::STATUS_PUBLISHED,
                'published_at' => now()->subDays($index + 1),
                'line_id' => $lines[$post[3]]->id,
                'category_id' => $category?->id,
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

    private function seedBonuses(\Illuminate\Support\Collection $lines, \Illuminate\Support\Collection $platforms, Agent $adminAgent, \Illuminate\Support\Collection $clients): \Illuminate\Support\Collection
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

    private function seedRaffles(\Illuminate\Support\Collection $lines, \Illuminate\Support\Collection $platforms, \Illuminate\Support\Collection $clients): void
    {
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
                ['position' => 1, 'name' => 'Viaje a Brasil', 'amount' => 7000, 'image' => $this->image('brasil-premio', 480, 260)],
                ['position' => 2, 'name' => 'Moto 0km', 'amount' => 3200, 'image' => $this->image('moto-premio', 480, 260)],
                ['position' => 3, 'name' => 'Notebook Pro 16', 'amount' => 1800, 'image' => $this->image('notebook-premio', 480, 260)],
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
                ['position' => 1, 'name' => 'Combo tecnologia', 'amount' => 1200, 'image' => $this->image('tech-premio', 480, 260)],
            ],
        ]);
        $finished->lines()->sync([$lines[2]->id]);
    }

    private function seedPromotions(\Illuminate\Support\Collection $lines, \Illuminate\Support\Collection $platforms): void
    {
        foreach ([
            ['Recarga caliente', 'Carga de viernes con beneficio extra para ruleta en vivo.', 'HOT30', 30],
            ['Noche VIP', 'Promocion para usuarios con actividad semanal.', 'VIPNIGHT', 50],
            ['Slots diarios', 'Bonificacion para jugar slots seleccionados.', 'SLOTS20', 20],
        ] as $index => $promo) {
            Promotion::withoutGlobalScopes()->create([
                'title' => $promo[0],
                'description' => $promo[1],
                'code' => $promo[2],
                'icon' => 'fa-solid fa-fire',
                'type' => 'percent',
                'bonus_percent' => $promo[3],
                'bonus_amount' => 0,
                'min_deposit' => 0,
                'max_bonus' => 70000,
                'is_recurring' => true,
                'recurring_days' => ['friday', 'saturday'],
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(20),
                'status' => 'published',
                'line_id' => $lines[$index]->id,
                'platform_id' => $platforms[$index]->id,
            ]);
        }
    }

    private function seedCarousel(\Illuminate\Support\Collection $lines): void
    {
        foreach ([
            ['hero-casino-live', 'Casino online con atencion real', '/lineas'],
            ['hero-bonos-activos', 'Bonos activos para jugar mas', '/#bonos'],
            ['hero-sorteo-vip', 'Sorteo VIP de la semana', '/sorteo'],
        ] as $index => $item) {
            CarouselItem::create([
                'image' => $this->image($item[0], 1600, 680),
                'title' => $item[1],
                'link' => $item[2],
                'order' => $index + 1,
                'line_id' => $lines[$index % $lines->count()]->id,
            ]);
        }
    }

    private function seedHomeConfig(\Illuminate\Support\Collection $posts, \Illuminate\Support\Collection $bonuses): void
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

    private function seedSupportData(\Illuminate\Support\Collection $lines, \Illuminate\Support\Collection $agents, \Illuminate\Support\Collection $clients, \Illuminate\Support\Collection $posts, \Illuminate\Support\Collection $platforms): void
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
        return 'https://picsum.photos/seed/redpicantes-'.$seed.'/'.$width.'/'.$height;
    }
}
