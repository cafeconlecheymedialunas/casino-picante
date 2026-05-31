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
use App\Models\NotificationPreference;
use App\Models\Setting;
use App\Models\UserNotification;
use App\Models\Vendor;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UnifiedDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Iniciando UnifiedDemoSeeder Completo...');

        // 1. Roles
        $roles = $this->seedRoles();

        // 2. Global Settings
        $this->seedSettings();

        // 3. Admin User & Agent
        $admin = $this->seedAdmin($roles);

        // 4. Platforms
        $platforms = $this->seedPlatforms();

        // 5. Categories
        $categories = $this->seedCategories();

        // 6. Official Vendor (Direct)
        $officialVendor = $this->seedOfficialVendor($admin, $platforms);

        // 7. Other Vendors (Cajeros)
        $vendors = $this->seedOtherVendors($roles, $platforms);

        // 8. Agents
        $agents = $this->seedAgents($roles, $officialVendor, $vendors);

        // 9. Lines for Official Vendor
        $officialLines = $this->seedOfficialLines($officialVendor, $platforms, $agents);

        // 10. Lines for Other Vendors
        $vendorLines = $this->seedVendorLines($vendors, $platforms, $agents);

        // 11. Clients
        $clients = $this->seedClients($roles, $officialLines, $vendorLines, $vendors);

        // 12. Bonuses
        $bonuses = $this->seedBonuses($officialVendor, $vendors, $officialLines, $vendorLines, $platforms, $admin);

        // 13. Raffles
        $raffles = $this->seedRaffles($officialVendor, $vendors, $officialLines, $vendorLines, $platforms, $clients);

        // 14. Posts & Comments
        $posts = $this->seedPosts($categories, $officialLines, $vendorLines, $clients, $agents);

        // 15. Carousel & Home Config
        $this->seedHomeGlobalContent($officialLines, $bonuses, $posts, $raffles, $vendors);

        // 16. Support Data (Sales, Tickets, Chats)
        $this->seedSupportData($officialLines->merge($vendorLines), $agents, $clients, $platforms);

        // 17. Bonus Assignments
        $this->seedBonusAssignments($bonuses, $clients);

        // 18. Notification Preferences & User Notifications
        $this->seedNotifications($agents, $clients);

        $this->command->info('UnifiedDemoSeeder completado con éxito.');
    }

    private function seedSettings(): void
    {
        $settings = [
            'site_title' => 'RED PICANTES',
            'site_logo' => '',
            'site_favicon' => 'favicon.ico',
            'contact_email' => 'soporte@redpicantes.test',
            'contact_phone' => '+54 9 11 4000-1000',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        $this->command->info('Configuraciones generales listas.');
    }

    private function seedNotifications($agents, $clients): void
    {
        foreach ($agents as $agent) {
            foreach (['tickets', 'ventas', 'bonos', 'sorteos'] as $module) {
                NotificationPreference::updateOrCreate(
                    ['agent_id' => $agent->id, 'module' => $module],
                    ['vendor_id' => $agent->vendor_id, 'is_enabled' => true]
                );
            }
        }

        foreach ($clients as $client) {
            UserNotification::create([
                'vendor_id' => $client->vendor_id,
                'user_id' => $client->id,
                'title' => '¡Bienvenido!',
                'message' => 'Gracias por unirte a RED PICANTES.',
                'type' => 'info',
            ]);
        }
        $this->command->info('Notificaciones y preferencias listas.');
    }

    private function seedBonusAssignments($bonuses, $clients): void
    {
        foreach ($clients as $c) {
            BonusAssignment::updateOrCreate(
                ['bonus_id' => $bonuses->random()->id, 'user_id' => $c->id],
                ['status' => 'active', 'assigned_at' => now()]
            );
        }
        $this->command->info('Asignaciones de bonos listas.');
    }

    private function seedRoles(): array
    {
        $roles = [];
        foreach ([
            Roles::ADMIN => 'Administrador',
            Roles::AGENTE => 'Agente',
            Roles::CLIENTE => 'Cliente',
            Roles::CAJERO => 'Cajero',
        ] as $name => $label) {
            $roles[$name] = Role::updateOrCreate(['name' => $name], ['label' => $label]);
        }
        $this->command->info('Roles listos.');
        return $roles;
    }

    private function seedAdmin(array $roles): User
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@redpicantes.test'],
            [
                'role_id' => $roles[Roles::ADMIN]->id,
                'username' => 'admin',
                'name' => 'Admin',
                'apellido' => 'General',
                'password' => Hash::make('password'),
                'phone' => '+54 9 11 4000-1000',
                'status' => 'active',
                'avatar' => $this->avatar('Admin General'),
            ]
        );

        Agent::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'username' => 'admin_red',
                'name' => 'Admin',
                'apellido' => 'General',
                'email' => $admin->email,
                'password' => Hash::make('password'),
                'phone' => $admin->phone,
                'status' => 'active',
                'cargo' => 'Administrador general',
                'avatar' => $admin->avatar,
            ]
        );

        $this->command->info('Admin listo.');
        return $admin;
    }

    private function seedPlatforms(): Collection
    {
        $data = [
            ['name' => 'VIP Casino',  'slug' => 'vip-casino',  'img' => '1051075', 'desc' => 'Slots y mesas premium con atención VIP.',             'contacts' => [['name' => 'Soporte VIP',     'type' => 'whatsapp', 'value' => 'https://wa.me/5491151003301']]],
            ['name' => 'Hybrid Club', 'slug' => 'hybrid-club', 'img' => '1047887', 'desc' => 'Torneos diarios y juegos en vivo de alta calidad.', 'contacts' => [['name' => 'Alta Hybrid',     'type' => 'telegram', 'value' => 'https://t.me/redpicantes']]],
            ['name' => 'Etoile Play', 'slug' => 'etoile-play', 'img' => '1055680', 'desc' => 'Experiencia simple, rápida y segura.',      'contacts' => [['name' => 'Mesa Etoile',     'type' => 'instagram', 'value' => 'https://instagram.com/redpicantes']]],
            ['name' => 'Golden Bet',  'slug' => 'golden-bet',  'img' => '1068523', 'desc' => 'Bonos de recarga diarios y premios increíbles.',       'contacts' => [['name' => 'Golden Atencion', 'type' => 'web',       'value' => 'https://redpicantes.test']]],
            ['name' => 'Royal Play',  'slug' => 'royal-play',  'img' => '1035',    'desc' => 'La elegancia del casino real en tu pantalla.', 'contacts' => [['name' => 'Mesa Royal', 'type' => 'whatsapp', 'value' => '5491100000000']]],
        ];

        $platforms = collect();
        foreach ($data as $p) {
            $platforms->push(Platform::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'name' => $p['name'],
                    'description' => $p['desc'],
                    'logo_url' => 'https://picsum.photos/id/'.$p['img'].'/256/256',
                    'website_url' => 'https://redpicantes.test/'.$p['slug'],
                    'is_active' => true,
                    'contacts' => $p['contacts'],
                ]
            ));
        }
        $this->command->info('Plataformas listas.');
        return $platforms;
    }

    private function seedCategories(): Collection
    {
        return collect(['Sorteos', 'Bonos', 'Casino online', 'Ganadores', 'Tutoriales', 'Estrategia'])->map(fn ($name) => Category::updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name]
        ));
    }

    private function seedOfficialVendor(User $admin, Collection $platforms): Vendor
    {
        $vendor = Vendor::updateOrCreate(
            ['slug' => 'cajero-oficial'],
            [
                'user_id' => $admin->id,
                'name' => 'Cajero Oficial',
                'description' => 'El cajero directo de la red Red Picantes. Cargas instantáneas, retiros seguros y atención humana sin intermediarios. Operamos con las mejores tasas del mercado.',
                'is_active' => true,
                'is_direct' => true,
                'logo' => 'https://picsum.photos/id/1025/256/256',
                'hero_image' => 'https://picsum.photos/id/1018/1400/500',
                'portrait_image' => 'https://picsum.photos/id/1016/600/800',
                'contacts' => [
                    ['name' => 'WhatsApp Oficial', 'type' => 'whatsapp', 'value' => '5491100000000'],
                    ['name' => 'Telegram Soporte', 'type' => 'telegram', 'value' => 'https://t.me/cajerooficial'],
                    ['name' => 'Instagram Novedades', 'type' => 'instagram', 'value' => 'https://instagram.com/redpicantes'],
                ],
                'features' => [
                    ['icon' => 'fa-solid fa-crown', 'title' => 'Red Oficial', 'desc' => 'Operado directamente por los fundadores.'],
                    ['icon' => 'fa-solid fa-bolt', 'title' => 'Carga Express', 'desc' => 'Saldo acreditado en menos de 2 minutos.'],
                    ['icon' => 'fa-solid fa-shield-halved', 'title' => 'Seguridad Total', 'desc' => 'Tus datos y fondos están protegidos.'],
                    ['icon' => 'fa-solid fa-headset', 'title' => 'Soporte 24/7', 'desc' => 'Estamos para ayudarte en cualquier momento.'],
                ],
            ]
        );

        $admin->update(['vendor_id' => $vendor->id]);

        $this->command->info('Vendor Oficial listo.');
        return $vendor;
    }

    private function seedOtherVendors(array $roles, Collection $platforms): Collection
    {
        $data = [
            [
                'slug' => 'betmaster',
                'name' => 'BetMaster Casino',
                'email' => 'cajero_betmaster@demo.com',
                'username' => 'betmaster',
                'logo' => '106', 'hero' => '160', 'portrait' => '201',
                'desc' => 'Expertos en apuestas deportivas y casino online. Trayectoria y confianza.',
            ],
            [
                'slug' => 'casino-royale',
                'name' => 'Casino Royale',
                'email' => 'cajero_royale@demo.com',
                'username' => 'royale',
                'logo' => '1011', 'hero' => '1015', 'portrait' => '1005',
                'desc' => 'La elegancia del juego en tus manos. Exclusividad y grandes premios.',
            ],
            [
                'slug' => 'pica-slots',
                'name' => 'Pica Slots',
                'email' => 'cajero_pica@demo.com',
                'username' => 'picaslots',
                'logo' => '1020', 'hero' => '1021', 'portrait' => '1022',
                'desc' => 'Tu lugar favorito para las maquinitas. Bonos de bienvenida imbatibles.',
            ],
            [
                'slug' => 'imperial-casino',
                'name' => 'Imperial Casino',
                'email' => 'cajero_imperial@demo.com',
                'username' => 'imperial',
                'logo' => '1031', 'hero' => '1032', 'portrait' => '1033',
                'desc' => 'Juego imperial para ganadores reales. Atención personalizada 24hs.',
            ],
        ];

        $vendors = collect();
        foreach ($data as $v) {
            $user = User::updateOrCreate(
                ['email' => $v['email']],
                [
                    'role_id' => $roles[Roles::CAJERO]->id,
                    'username' => $v['username'],
                    'name' => $v['name'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'avatar' => $this->avatar($v['name']),
                ]
            );

            $vendor = Vendor::updateOrCreate(
                ['slug' => $v['slug']],
                [
                    'user_id' => $user->id,
                    'name' => $v['name'],
                    'description' => $v['desc'],
                    'logo' => 'https://picsum.photos/id/'.$v['logo'].'/256/256',
                    'hero_image' => 'https://picsum.photos/id/'.$v['hero'].'/1400/500',
                    'portrait_image' => 'https://picsum.photos/id/'.$v['portrait'].'/600/800',
                    'is_active' => true,
                    'is_direct' => false,
                    'contacts' => [['name' => 'WhatsApp', 'type' => 'whatsapp', 'value' => '549115555'.rand(1000, 9999)]],
                    'features' => [
                        ['icon' => 'fa-solid fa-star', 'title' => 'Cajero Verificado', 'desc' => 'Garantía de cumplimiento.'],
                        ['icon' => 'fa-solid fa-gift', 'title' => 'Bonos Diarios', 'desc' => 'Más chances de ganar todos los días.'],
                    ]
                ]
            );

            $user->update(['vendor_id' => $vendor->id]);
            $vendors->push($vendor);
        }

        $this->command->info('Vendors secundarios listos.');
        return $vendors;
    }

    private function seedAgents(array $roles, Vendor $official, Collection $others): Collection
    {
        $names = ['Sofia Paz', 'Bruno Rivas', 'Micaela Luna', 'Nicolas Vega', 'Valentina Sol', 'Mateo Cruz', 'Camila Flor', 'Lucas Mar'];
        $agents = collect();

        foreach ($names as $i => $fullName) {
            [$name, $last] = explode(' ', $fullName);
            $email = Str::slug($name).'@redpicantes.test';
            $vendor = $i < 3 ? $official : $others->random();

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'role_id' => $roles[Roles::AGENTE]->id,
                    'username' => Str::slug($fullName),
                    'name' => $name,
                    'apellido' => $last,
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'vendor_id' => $vendor->id,
                    'avatar' => $this->avatar($fullName),
                    'phone' => '+54 9 11 5100-'.(2200 + $i),
                ]
            );

            $agents->push(Agent::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'username' => $user->username,
                    'name' => $name,
                    'apellido' => $last,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'vendor_id' => $vendor->id,
                    'cargo' => $i === 0 ? 'Super Agente' : 'Atención al Cliente',
                    'avatar' => $user->avatar,
                    'phone' => $user->phone,
                ]
            ));
        }
        $this->command->info('Agentes listos.');
        return $agents;
    }

    private function seedOfficialLines(Vendor $vendor, Collection $platforms, Collection $agents): Collection
    {
        $lines = collect();
        $lineNames = [
            'Línea Fuego VIP' => 'Atención prioritaria para altas, recargas grandes y beneficios exclusivos.',
            'Línea Ruleta Pro' => 'Línea enfocada en ruleta en vivo y mesas rápidas.',
            'Línea Slots Express' => 'Alta rápida y bonos pensados para slots.',
            'Línea Norte RP' => 'Atención regional con soporte humano especializado.',
        ];

        $i = 0;
        foreach ($lineNames as $name => $desc) {
            $plat = $platforms->get($i % $platforms->count());
            $line = Line::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'vendor_id' => $vendor->id,
                    'name' => $name,
                    'type' => $i === 0 ? 'vip' : ($i === 1 ? 'pro' : 'express'),
                    'phone' => '+54 9 11 0000-000'.($i + 1),
                    'description' => $desc,
                    'status' => 'active',
                    'perfil_url' => 'https://picsum.photos/id/'.(1030 + $i).'/256/256',
                    'portada_url' => 'https://picsum.photos/id/'.(1040 + $i).'/1200/400',
                    'contact_links' => [
                        ['name' => 'WhatsApp VIP', 'type' => 'whatsapp', 'value' => '549110000000'.($i + 1)],
                        ['name' => 'Telegram', 'type' => 'telegram', 'value' => 'https://t.me/redpicantes'],
                    ],
                ]
            );

            $line->platforms()->sync([$plat->id => ['vendor_id' => $vendor->id, 'is_active' => true]]);

            // Assign Agent as Encargado
            $agent = $agents->where('vendor_id', $vendor->id)->values()->get($i % $agents->where('vendor_id', $vendor->id)->count());
            if ($agent) {
                LineAgent::updateOrCreate(
                    ['line_id' => $line->id, 'agent_id' => $agent->id],
                    ['role' => LineRoles::ENCARGADO, 'is_active' => true, 'vendor_id' => $vendor->id, 'porcentaje_ganancia' => 30]
                );
                foreach (Permissions::all() as $perm) {
                    LineAgentPermission::updateOrCreate(
                        ['line_id' => $line->id, 'agent_id' => $agent->id, 'permission' => $perm],
                        ['vendor_id' => $vendor->id]
                    );
                }
            }

            $lines->push($line);
            $i++;
        }
        $this->command->info('Lineas oficiales listas.');
        return $lines;
    }

    private function seedVendorLines(Collection $vendors, Collection $platforms, Collection $agents): Collection
    {
        $lines = collect();
        foreach ($vendors as $v) {
            foreach (range(1, 2) as $i) {
                $name = $v->name.' — Línea '.($i === 1 ? 'Principal' : 'Secundaria');
                $line = Line::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'vendor_id' => $v->id,
                        'name' => $name,
                        'type' => $i === 1 ? 'pro' : 'express',
                        'phone' => '+54 9 11 5555-'.rand(1000, 9999),
                        'description' => 'Línea de atención oficial de '.$v->name.'. Cargas y retiros al instante.',
                        'status' => 'active',
                        'perfil_url' => 'https://picsum.photos/id/'.rand(100, 200).'/256/256',
                        'portada_url' => 'https://picsum.photos/id/'.rand(100, 200).'/1200/400',
                        'contact_links' => [['name' => 'Atención WhatsApp', 'type' => 'whatsapp', 'value' => '5491100000000']],
                    ]
                );

                $line->platforms()->sync($platforms->random(2)->pluck('id')->mapWithKeys(fn($id) => [$id => ['vendor_id' => $v->id, 'is_active' => true]]));

                $agent = $agents->where('vendor_id', $v->id)->first();
                if ($agent) {
                    LineAgent::updateOrCreate(
                        ['line_id' => $line->id, 'agent_id' => $agent->id],
                        ['role' => LineRoles::ENCARGADO, 'is_active' => true, 'vendor_id' => $v->id, 'porcentaje_ganancia' => 25]
                    );
                    foreach (Permissions::all() as $perm) {
                        LineAgentPermission::updateOrCreate(['line_id' => $line->id, 'agent_id' => $agent->id, 'permission' => $perm, 'vendor_id' => $v->id]);
                    }
                }

                $lines->push($line);
            }
        }
        $this->command->info('Lineas de vendors listas.');
        return $lines;
    }

    private function seedClients(array $roles, Collection $officialLines, Collection $vendorLines, Collection $vendors): Collection
    {
        $names = ['Tomas Medina', 'Camila Soria', 'Lucas Pereyra', 'Martina Suarez', 'Diego Correa', 'Luciana Vega', 'Facundo Rios', 'Elena Paz', 'Geronimo Sol', 'Victoria Luna'];
        $clients = collect();

        foreach ($names as $i => $fullName) {
            [$name, $last] = explode(' ', $fullName);
            $email = 'cliente'.($i + 1).'@demo.test';
            $vendor = $i < 4 ? Vendor::where('slug', 'cajero-oficial')->first() : $vendors->random();
            $line = $vendor->is_direct ? $officialLines->random() : $vendorLines->where('vendor_id', $vendor->id)->random();

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'role_id' => $roles[Roles::CLIENTE]->id,
                    'username' => 'user'.($i + 1),
                    'name' => $name,
                    'apellido' => $last,
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'vendor_id' => $vendor->id,
                    'line_id' => $line->id,
                    'avatar' => $this->avatar($fullName),
                    'phone' => '+54 9 11 6000-'.(1000 + $i),
                ]
            );

            $user->lines()->sync([$line->id => ['vendor_id' => $vendor->id, 'is_active' => true]]);
            $clients->push($user);
        }
        $this->command->info('Clientes listos.');
        return $clients;
    }

    private function seedBonuses(Vendor $official, Collection $others, Collection $oLines, Collection $vLines, Collection $plats, User $admin): Collection
    {
        $bonuses = collect();
        // Official Bonuses
        $officialData = [
            ['BIENVENIDA-100', 'Bono Bienvenida 100%', 'Duplicamos tu primera carga en cualquier plataforma oficial.', 100, 20000],
            ['RECARGA-FINDE', 'Bono Finde XL 50%', 'Recargas los días sábados y domingos con 50% de regalo.', 50, 10000],
            ['VIP-RP-200', 'Bono VIP Platinum 200%', 'Exclusivo para clientes con más de 1M en jugadas mensuales.', 200, 100000],
        ];

        foreach ($officialData as $idx => $bd) {
            $bonuses->push(Bonus::withoutGlobalScopes()->updateOrCreate(
                ['code' => $bd[0]],
                [
                    'vendor_id' => $official->id,
                    'slug' => Str::slug($bd[1]),
                    'title' => $bd[1],
                    'description' => $bd[2],
                    'bonus_percent' => $bd[3],
                    'max_bonus' => $bd[4],
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'line_id' => $oLines->random()->id,
                    'platform_id' => $plats->random()->id,
                    'created_by' => Agent::where('vendor_id', $official->id)->first()?->id,
                ]
            ));
        }

        // Vendor Bonuses
        foreach ($others as $v) {
            $line = $vLines->where('vendor_id', $v->id)->first();
            $bonuses->push(Bonus::withoutGlobalScopes()->updateOrCreate(
                ['code' => 'PROMO-'.strtoupper($v->slug)],
                [
                    'vendor_id' => $v->id,
                    'slug' => 'promo-'.Str::slug($v->name),
                    'title' => 'Promo Especial '.$v->name,
                    'description' => 'Bono exclusivo de la red '.$v->name.' para todos sus jugadores activos.',
                    'bonus_percent' => 30,
                    'max_bonus' => 5000,
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'line_id' => $line->id,
                    'platform_id' => $plats->random()->id,
                    'created_by' => Agent::where('vendor_id', $v->id)->first()?->id,
                ]
            ));
        }
        $this->command->info('Bonos listos.');
        return $bonuses;
    }

    private function seedRaffles(Vendor $official, Collection $others, Collection $oLines, Collection $vLines, Collection $plats, Collection $clients): Collection
    {
        $raffles = collect();
        // Official Raffle
        $rafflesData = [
            [
                'slug' => 'gran-sorteo-mensual',
                'title' => 'Gran Sorteo Mensual RP',
                'desc' => 'Participá con todas tus cargas del mes. ¡Premios en efectivo y gadgets!',
                'status' => 'active',
                'prizes' => [
                    ['position' => 1, 'name' => 'iPhone 15 Pro Max', 'amount' => 12000, 'image' => 'https://picsum.photos/id/160/480/260'],
                    ['position' => 2, 'name' => 'MacBook Air M2', 'amount' => 8000, 'image' => 'https://picsum.photos/id/0/480/260'],
                    ['position' => 3, 'name' => '$50.000 en Fichas', 'amount' => 5000, 'image' => 'https://picsum.photos/id/1025/480/260'],
                ]
            ],
            [
                'slug' => 'sorteo-relampago-vip',
                'title' => 'Sorteo Relámpago VIP',
                'desc' => 'Solo para clientes de la Línea Fuego VIP. Se sortea este viernes.',
                'status' => 'active',
                'prizes' => [
                    ['position' => 1, 'name' => '$100.000 Cash', 'amount' => 10000, 'image' => 'https://picsum.photos/id/102/480/260'],
                ]
            ]
        ];

        foreach ($rafflesData as $rd) {
            $r = Raffle::withoutGlobalScopes()->updateOrCreate(
                ['slug' => $rd['slug']],
                [
                    'vendor_id' => $official->id,
                    'title' => $rd['title'],
                    'description' => $rd['desc'],
                    'status' => $rd['status'],
                    'start_date' => now()->subDays(5),
                    'end_date' => now()->addDays(10),
                    'start_number' => 1,
                    'end_number' => 500,
                    'line_id' => $oLines->first()->id,
                    'platform_id' => $plats->first()->id,
                    'prizes' => $rd['prizes'],
                ]
            );
            $r->lines()->sync($oLines->pluck('id'));
            $raffles->push($r);

            // Assign some numbers
            foreach ($clients->random(5) as $idx => $c) {
                RaffleNumber::updateOrCreate(
                    ['raffle_id' => $r->id, 'number' => (10 + $idx)],
                    ['user_id' => $c->id, 'line_id' => $oLines->first()->id, 'vendor_id' => $official->id]
                );
            }
        }

        $this->command->info('Sorteos listos.');
        return $raffles;
    }

    private function seedPosts(Collection $categories, Collection $oLines, Collection $vLines, Collection $clients, Collection $agents): Collection
    {
        $posts = collect();
        $allLines = $oLines->merge($vLines);
        
        $titles = [
            '¡Nuevos Slots Temáticos en VIP Casino!' => 'Descubrí las nuevas máquinas inspiradas en el antiguo Egipto. Con bonos especiales de lanzamiento.',
            'Ganadores de la Semana: Julio se llevó 2M' => 'Felicitamos a nuestro cliente Julio de la línea Ruleta Pro por su gran jugada en la ruleta en vivo.',
            'Cómo aprovechar al máximo tus bonos de recarga' => 'Tutorial paso a paso para usar tus códigos promocionales y duplicar tus chances de ganar.',
            'Próximo Gran Sorteo: ¡Un viaje a Brasil!' => 'No te pierdas la oportunidad de viajar con todo pago. Participá cargando saldo esta semana.',
            'Nuevos métodos de retiro instantáneo' => 'Ahora podés retirar tus ganancias por Mercado Pago y transferencia en menos de 5 minutos.',
            'Estrategias para Ruleta: Qué saber antes de jugar' => 'Consejos de nuestros expertos para que tu experiencia en la mesa sea divertida y ganadora.',
            'Mantenimiento Programado en Plataforma Hybrid' => 'Aviso importante: Mañana de 04:00 a 06:00 hs la plataforma estará fuera de línea por mejoras.',
            'Entrevista con un Super Agente de Red Picantes' => 'Conocé la historia de Sofia y cómo ayuda a cientos de clientes a disfrutar del casino cada día.',
        ];

        $i = 0;
        foreach ($titles as $title => $excerpt) {
            $line = $allLines->random();
            $created = Post::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'content' => "<h3>{$title}</h3><p>{$excerpt}</p><p>En Red Picantes trabajamos para brindarte la mejor experiencia de juego online de Argentina. Recordá siempre jugar con responsabilidad.</p><p>Podés consultar con tu línea asignada sobre esta novedad o cualquier duda que tengas.</p>",
                    'excerpt' => $excerpt,
                    'image' => 'https://picsum.photos/id/'.(1040 + $i).'/900/560',
                    'status' => Post::STATUS_PUBLISHED,
                    'published_at' => now()->subDays($i),
                    'line_id' => $line->id,
                    'vendor_id' => $line->vendor_id,
                    'category_id' => $categories->random()->id,
                    'author_agent_id' => $agents->where('vendor_id', $line->vendor_id)->first()?->id ?? $agents->first()->id,
                ]
            );
            $posts->push($created);

            // Add some comments
            foreach (range(1, rand(1, 3)) as $cIdx) {
                Comment::create([
                    'post_id' => $created->id,
                    'user_id' => $clients->random()->id,
                    'content' => '¡Excelente información! Muy útil para los que recién empezamos.',
                    'is_approved' => true,
                ]);
            }
            $i++;
        }
        $this->command->info('Posts y comentarios listos.');
        return $posts;
    }

    private function seedHomeGlobalContent($lines, $bonuses, $posts, $raffles, $vendors): void
    {
        // Global Carousel
        $carouselItems = [
            [
                'title' => 'Casino Online con Atención Real 24/7',
                'description' => 'Elegí una línea activa, pedí tu usuario y empezá a jugar con soporte humano desde el primer mensaje.',
                'cta_text' => 'Ver Líneas Activas',
                'image' => 'https://picsum.photos/id/1057/1600/680',
                'link' => '/lineas',
            ],
            [
                'title' => 'Bonos de Recarga hasta el 100%',
                'description' => 'Promociones vigentes, recargas con ventaja y beneficios seleccionados para que tu saldo rinda mucho más.',
                'cta_text' => 'Explorar Bonos',
                'image' => 'https://picsum.photos/id/1058/1600/680',
                'link' => '/bonos',
            ],
            [
                'title' => 'Sorteos Semanales con Premios Reales',
                'description' => 'Participá en sorteos activos de iPhones, Macs y crédito en fichas. Seguí las fechas en vivo.',
                'cta_text' => 'Ver Sorteos',
                'image' => 'https://picsum.photos/id/1059/1600/680',
                'link' => '/sorteos',
            ],
        ];

        foreach ($carouselItems as $i => $data) {
            $item = CarouselItem::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => null, 'title' => $data['title']],
                array_merge($data, ['order' => $i + 1])
            );

            HomeConfig::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => null, 'section' => HomeConfig::SECTION_CAROUSEL, 'item_id' => $item->id],
                ['order' => $i]
            );
        }

        // Home Sections
        $sections = [
            [
                'section_key' => 'como-empezar',
                'title' => 'Empezá en',
                'highlight' => '3 pasos',
                'subtitle' => 'Sin vueltas ni formularios eternos: contacto directo, carga rápida y juego inmediato.',
                'repeater_data' => [
                    ['title' => '1. Elegí tu Línea', 'subtitle' => 'Seleccioná la línea que más te guste y contactá por WhatsApp.', 'icon' => 'fa-solid fa-layer-group'],
                    ['title' => '2. Carga Saldo', 'subtitle' => 'Realizá tu primera carga con los medios de pago disponibles.', 'icon' => 'fa-solid fa-wallet'],
                    ['title' => '3. A Jugar!', 'subtitle' => 'Ingresá a la plataforma con tu usuario y disfrutá de los mejores slots.', 'icon' => 'fa-solid fa-gamepad'],
                ],
            ],
            [
                'section_key' => 'lineas',
                'title' => 'Líneas de',
                'highlight' => 'Atención',
                'line_ids' => $lines->pluck('id')->toArray(),
            ],
            [
                'section_key' => 'sorteo',
                'title' => 'PRÓXIMOS',
                'highlight' => 'SORTEOS',
                'raffle_type' => 'active',
                'raffle_ids' => $raffles->pluck('id')->toArray(),
            ],
            [
                'section_key' => 'bonos',
                'title' => 'Bonos',
                'highlight' => 'Activos',
                'bonus_type' => 'active',
                'bonus_ids' => $bonuses->pluck('id')->toArray(),
            ],
            [
                'section_key' => 'blog',
                'title' => 'Noticias y',
                'highlight' => 'Novedades',
                'post_ids' => $posts->pluck('id')->toArray(),
            ],
        ];

        foreach ($sections as $i => $s) {
            HomeSection::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => null, 'section_key' => $s['section_key']],
                array_merge($s, ['enabled' => true, 'order' => $i])
            );
        }

        // Public Pages (Tabs Structure)
        $officialLineIds = $lines->where('vendor_id', Vendor::where('slug', 'cajero-oficial')->first()->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $cajeroLineIds = $lines->where('vendor_id', '!=', Vendor::where('slug', 'cajero-oficial')->first()->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $officialBonusIds = $bonuses->where('vendor_id', Vendor::where('slug', 'cajero-oficial')->first()->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $cajeroBonusIds = $bonuses->where('vendor_id', '!=', Vendor::where('slug', 'cajero-oficial')->first()->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $officialRaffleIds = $raffles->where('vendor_id', Vendor::where('slug', 'cajero-oficial')->first()->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $cajeroRaffleIds = $raffles->where('vendor_id', '!=', Vendor::where('slug', 'cajero-oficial')->first()->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();

        $pages = [
            [
                'section_key' => 'page.lineas',
                'title' => 'Elegí una Línea',
                'highlight' => 'Activa',
                'repeater_data' => [
                    ['key' => 'red-picantes', 'title' => 'Red Picantes', 'subtitle' => 'Líneas oficiales de la red, mayor respaldo y rapidez.', 'enabled' => true, 'icon' => 'fa-solid fa-crown', 'item_ids' => $officialLineIds],
                    ['key' => 'cajeros', 'title' => 'Cajeros Verificados', 'subtitle' => 'Líneas activas publicadas por cajeros independientes verificados.', 'enabled' => true, 'icon' => 'fa-solid fa-cash-register', 'item_ids' => $cajeroLineIds],
                ],
            ],
            [
                'section_key' => 'page.bonos',
                'title' => 'Bonos',
                'highlight' => 'Activos',
                'repeater_data' => [
                    ['key' => 'red-picantes', 'title' => 'Promos Oficiales', 'subtitle' => 'Beneficios exclusivos del cajero directo de la red.', 'enabled' => true, 'icon' => 'fa-solid fa-crown', 'item_ids' => $officialBonusIds],
                    ['key' => 'cajeros', 'title' => 'Bonos de Cajeros', 'subtitle' => 'Promociones publicadas por otros cajeros de la plataforma.', 'enabled' => true, 'icon' => 'fa-solid fa-cash-register', 'item_ids' => $cajeroBonusIds],
                ],
            ],
            [
                'section_key' => 'page.sorteos',
                'title' => 'Sorteos',
                'highlight' => 'Activos',
                'repeater_data' => [
                    ['key' => 'red-picantes', 'title' => 'Sorteos de la Red', 'subtitle' => 'Participá en los grandes sorteos oficiales de Red Picantes.', 'enabled' => true, 'icon' => 'fa-solid fa-crown', 'item_ids' => $officialRaffleIds],
                    ['key' => 'cajeros', 'title' => 'Sorteos Locales', 'subtitle' => 'Sorteos organizados por cada cajero para su propia comunidad.', 'enabled' => true, 'icon' => 'fa-solid fa-cash-register', 'item_ids' => $cajeroRaffleIds],
                ],
            ],
            [
                'section_key' => 'page.cajeros',
                'title' => 'Elegí tu',
                'highlight' => 'Cajero',
                'repeater_data' => [
                    ['key' => 'general', 'title' => 'Cajeros Disponibles', 'subtitle' => 'Listado de todos los cajeros verificados que operan en nuestra red.', 'enabled' => true, 'icon' => 'fa-solid fa-cash-register', 'item_ids' => $vendors->pluck('id')->map(fn($id) => (string)$id)->toArray()],
                ],
            ],
            [
                'section_key' => 'page.novedades',
                'title' => 'Novedades de',
                'highlight' => 'RED PICANTES',
                'repeater_data' => [
                    ['key' => 'general', 'title' => 'Últimas Noticias', 'subtitle' => 'Mantenete al día con bonos, sorteos y consejos de juego.', 'enabled' => true, 'icon' => 'fa-solid fa-newspaper', 'item_ids' => $posts->pluck('id')->map(fn($id) => (string)$id)->toArray()],
                ],
            ],
        ];

        foreach ($pages as $p) {
            HomeSection::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => null, 'section_key' => $p['section_key']],
                array_merge($p, ['enabled' => true, 'order' => 0])
            );
        }

        $this->command->info('Contenido global de Home listo.');
    }

    private function seedSupportData($lines, $agents, $clients, $platforms): void
    {
        foreach ($clients as $c) {
            $line = $lines->where('id', $c->line_id)->first() ?? $lines->random();
            $agent = $agents->where('vendor_id', $line->vendor_id)->first() ?? $agents->first();

            // Create multiple sales per client
            foreach (range(1, rand(2, 4)) as $sIdx) {
                Sale::create([
                    'line_id' => $line->id,
                    'agent_id' => $agent->id,
                    'client_id' => $c->id,
                    'platform_id' => $platforms->random()->id,
                    'fecha_inicio' => now()->subDays(rand(1, 30)),
                    'monto_fichas' => rand(5000, 80000),
                    'ganancia_superagente' => rand(500, 8000),
                ]);
            }

            // Create ticket with conversation
            $t = Ticket::create([
                'user_id' => $c->id,
                'line_id' => $line->id,
                'subject' => 'Consulta operativa - ' . $c->name,
                'status' => $c->id % 2 === 0 ? 'open' : 'closed',
                'priority' => 'medium',
                'category' => 'Cargas',
            ]);

            TicketMessage::create(['ticket_id' => $t->id, 'user_id' => $c->id, 'message' => 'Hola, cargué saldo hace 10 minutos y todavía no lo veo reflejado en la plataforma.']);
            TicketMessage::create(['ticket_id' => $t->id, 'agent_id' => $agent->id, 'message' => 'Hola ' . $c->name . ', lo estamos revisando. Recordá que a veces la plataforma demora unos minutos en actualizar.']);
            TicketMessage::create(['ticket_id' => $t->id, 'user_id' => $c->id, 'message' => 'Perfecto, ¡ahora ya me apareció! Muchas gracias.']);

            DashboardNotification::create([
                'agent_id' => $agent->id,
                'title' => 'Nuevo ticket de ' . $c->name,
                'message' => 'El cliente consultó por una carga pendiente.',
                'type' => 'info',
                'link' => '/admin/tickets',
                'module' => 'tickets',
            ]);

            LineRating::create([
                'line_id' => $line->id,
                'user_id' => $c->id,
                'rating' => 5,
                'message' => 'La atención de ' . $agent->name . ' fue excelente. Me cargó al toque.',
            ]);

            // Real Chat
            $chat = Chat::create([
                'user_id' => $c->id,
                'agent_id' => $agent->id,
                'subject' => 'Consulta por Bono',
                'status' => 'open',
            ]);

            ChatMessage::create(['chat_id' => $chat->id, 'user_id' => $c->id, 'message' => 'Hola, ¿qué bonos hay activos hoy?']);
            ChatMessage::create(['chat_id' => $chat->id, 'agent_id' => $agent->id, 'message' => 'Hola! Hoy tenemos un 50% de recarga por el Finde XL.']);
        }
        $this->command->info('Datos de soporte y ventas listos.');
    }

    private function avatar(string $name): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=ff6a1a&color=ffffff&bold=true&size=256';
    }
}
