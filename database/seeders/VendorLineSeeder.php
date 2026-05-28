<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\CarouselItem;
use App\Models\Category;
use App\Models\Comment;
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
use App\Models\User;
use App\Models\Vendor;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Support\Roles;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorLineSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== VendorLineSeeder ===');

        $roles = $this->ensureRoles();
        $vendors = $this->seedVendors($roles);
        $platforms = $this->seedPlatforms($vendors);
        $agents = $this->seedAgents($vendors, $roles);
        $lines = $this->seedLines($vendors, $agents, $platforms);
        $categories = $this->seedCategories($vendors);
        $clients = $this->seedClients($vendors, $roles, $lines);
        $posts = $this->seedPosts($vendors, $categories, $lines, $agents, $clients);
        $bonuses = $this->seedBonuses($vendors, $lines, $platforms, $agents, $clients);
        $raffles = $this->seedRaffles($vendors, $lines, $platforms, $clients);
        $this->seedSales($vendors, $lines, $agents, $clients, $platforms);
        $this->seedCarousel($vendors, $lines);
        $this->seedRatings($vendors, $lines, $clients);
        $this->seedHomeSections($vendors, $lines, $bonuses, $raffles, $posts);

        $this->command->info('=== VendorLineSeeder completo ===');
    }

    private function ensureRoles(): array
    {
        $roles = [];
        foreach ([
            Roles::ADMIN => 'Admin',
            Roles::AGENTE => 'Agente',
            Roles::CLIENTE => 'Cliente',
            Roles::CAJERO => 'Cajero',
        ] as $name => $label) {
            $roles[$name] = Role::firstOrCreate(['name' => $name], ['label' => $label]);
        }

        return $roles;
    }

    private function seedVendors(array $roles): array
    {
        $data = [
            [
                'name' => 'Casino Royale',
                'slug' => 'casino-royale',
                'description' => 'Casino premium con atencion VIP y plataformas exclusivas.',
                'cajero' => ['Cajero Royale', 'cajero.royale@demo.com', 'cajero1'],
            ],
            [
                'name' => 'BetMaster',
                'slug' => 'betmaster',
                'description' => 'Apuestas deportivas, casino en vivo y bonos diarios.',
                'cajero' => ['Cajero BetMaster', 'cajero.betmaster@demo.com', 'betmaster'],
            ],
            [
                'name' => 'Lucky Spin',
                'slug' => 'lucky-spin',
                'description' => 'Slots, ruleta y promociones semanales con atencion rapida.',
                'cajero' => ['Cajero Lucky', 'cajero.lucky@demo.com', 'luckycajero'],
            ],
        ];

        $vendors = [];
        foreach ($data as $i => $vd) {
            $username = 'cajero_'.$vd['slug'];
            $user = User::updateOrCreate(
                ['email' => $vd['cajero'][1]],
                [
                    'name' => $vd['cajero'][0],
                    'username' => $username,
                    'password' => Hash::make('password'),
                    'role_id' => $roles[Roles::CAJERO]->id,
                    'phone' => '+5491100000'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                    'status' => 'active',
                ]
            );

            $vendors[] = Vendor::updateOrCreate(
                ['slug' => $vd['slug']],
                [
                    'user_id' => $user->id,
                    'name' => $vd['name'],
                    'description' => $vd['description'],
                    'logo' => 'https://ui-avatars.com/api/?name='.urlencode($vd['name']).'&background=ff6a1a&color=fff&bold=true&size=256',
                    'is_active' => true,
                    'contacts' => [
                        ['type' => 'whatsapp', 'value' => 'https://wa.me/54911'.(10000000 + $i), 'name' => 'WhatsApp '.$vd['name']],
                    ],
                    'branding' => ['primary' => '#ff6a1a', 'accent' => '#ffb347'],
                ]
            );
        }

        $this->command->info(count($vendors).' vendors creados.');

        return $vendors;
    }

    private function seedPlatforms(array $vendors): array
    {
        $names = ['VIP Casino', 'Hybrid Club', 'Etoile Play', 'Golden Bet'];
        $platforms = [];

        foreach ($vendors as $vendor) {
            foreach ($names as $j => $name) {
                $slug = Str::slug($name).'-'.$vendor->slug;
                $platforms[] = Platform::withoutGlobalScopes()->updateOrCreate(
                    ['vendor_id' => $vendor->id, 'slug' => $slug],
                    [
                        'name' => $name.' ('.$vendor->name.')',
                        'description' => $name.' disponible en '.$vendor->name,
                        'logo_url' => 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=ff6a1a&color=fff&bold=true&size=128',
                        'website_url' => 'https://'.$vendor->slug.'.test/'.$slug,
                        'is_active' => true,
                        'contacts' => [
                            ['type' => 'whatsapp', 'value' => 'https://wa.me/54911'.(20000000 + $j), 'name' => 'Soporte '.$name],
                        ],
                    ]
                );
            }
        }

        $this->command->info(count($platforms).' plataformas creadas.');

        return $platforms;
    }

    private function seedAgents(array $vendors, array $roles): array
    {
        $agentData = [
            ['Sofia', 'Paz', 'sofia', 'sofia@demo.com', 'Super agente'],
            ['Bruno', 'Rivas', 'bruno', 'bruno@demo.com', 'Atencion VIP'],
            ['Micaela', 'Luna', 'mica', 'mica@demo.com', 'Cargas y retiros'],
        ];

        $agents = [];
        foreach ($vendors as $vi => $vendor) {
            foreach ($agentData as $ai => $ad) {
                $email = $ad[2].'_'.$vendor->slug.'@demo.com';
                $username = $ad[2].'_'.$vendor->slug;

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $ad[0],
                        'apellido' => $ad[1],
                        'username' => $username,
                        'password' => Hash::make('password'),
                        'phone' => '+54911'.(30000000 + $vi * 100 + $ai),
                        'role_id' => $roles[Roles::AGENTE]->id,
                        'vendor_id' => $vendor->id,
                        'status' => 'active',
                    ]
                );

                $agents[] = Agent::withoutGlobalScopes()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'vendor_id' => $vendor->id,
                        'username' => $username,
                        'name' => $ad[0],
                        'apellido' => $ad[1],
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'phone' => $user->phone,
                        'cargo' => $ad[4],
                        'status' => 'active',
                    ]
                );
            }
        }

        $this->command->info(count($agents).' agentes creados.');

        return $agents;
    }

    private function seedLines(array $vendors, array $agents, array $platforms): array
    {
        $lineConfigs = [
            [
                'name' => 'Linea Premium',
                'type' => 'vip',
                'description' => 'Atencion premium con bonos exclusivos y plataformas de alta gama.',
            ],
            [
                'name' => 'Linea Rapida',
                'type' => 'express',
                'description' => 'Altas rapidas, recargas y soporte inmediato.',
            ],
            [
                'name' => 'Linea Clasica',
                'type' => 'general',
                'description' => 'Servicio general con todas las plataformas disponibles.',
            ],
        ];

        $lines = [];
        $agentIndex = 0;

        foreach ($vendors as $vi => $vendor) {
            $vendorAgents = array_values(array_filter($agents, fn ($a) => (int) $a->vendor_id === (int) $vendor->id));
            $vendorPlatforms = array_values(array_filter($platforms, fn ($p) => (int) $p->vendor_id === (int) $vendor->id));

            foreach ($lineConfigs as $li => $lc) {
                $line = Line::withoutGlobalScopes()->create([
                    'vendor_id' => $vendor->id,
                    'name' => $lc['name'].' '.$vendor->name,
                    'type' => $lc['type'],
                    'phone' => '+54911'.(40000000 + $vi * 100 + $li),
                    'icon' => ['fa-solid fa-crown', 'fa-solid fa-bolt', 'fa-solid fa-star'][$li],
                    'description' => $lc['description'],
                    'status' => 'active',
                    'contact_links' => [
                        ['name' => 'WhatsApp', 'type' => 'whatsapp', 'value' => 'https://wa.me/54911'.(40000000 + $vi * 100 + $li)],
                        ['name' => 'Telegram', 'type' => 'telegram', 'value' => 'https://t.me/'.$vendor->slug.'_linea'.($li + 1)],
                    ],
                    'portada_url' => 'https://picsum.photos/seed/'.$vendor->slug.'-linea'.($li + 1).'/1200/420',
                    'perfil_url' => 'https://ui-avatars.com/api/?name='.urlencode($lc['name']).'&background=ff6a1a&color=fff&bold=true&size=256',
                    'best_sales' => random_int(800000, 3500000),
                ]);

                // Asignar plataformas
                if (count($vendorPlatforms) > 0) {
                    $linePlatforms = array_slice($vendorPlatforms, 0, min(3, count($vendorPlatforms)));
                    $syncData = [];
                    foreach ($linePlatforms as $lp) {
                        $syncData[$lp->id] = [
                            'vendor_id' => $vendor->id,
                            'custom_message' => 'Disponible en '.$line->name.' con soporte dedicado.',
                            'is_active' => true,
                        ];
                    }
                    $line->platforms()->sync($syncData);
                }

                // Asignar encargado
                if (count($vendorAgents) > 0) {
                    $manager = $vendorAgents[$li % count($vendorAgents)];
                    $la = LineAgent::create([
                        'vendor_id' => $vendor->id,
                        'line_id' => $line->id,
                        'agent_id' => $manager->id,
                        'role' => LineRoles::ENCARGADO,
                        'is_active' => true,
                        'porcentaje_ganancia' => random_int(20, 40),
                    ]);

                    foreach (Permissions::all() as $perm) {
                        LineAgentPermission::create([
                            'vendor_id' => $vendor->id,
                            'line_id' => $line->id,
                            'agent_id' => $manager->id,
                            'permission' => $perm,
                        ]);
                    }

                    // Asignar miembros
                    foreach ($vendorAgents as $member) {
                        if ((int) $member->id === (int) $manager->id) {
                            continue;
                        }
                        LineAgent::create([
                            'vendor_id' => $vendor->id,
                            'line_id' => $line->id,
                            'agent_id' => $member->id,
                            'role' => LineRoles::MIEMBRO,
                            'parent_id' => $manager->id,
                            'is_active' => true,
                            'porcentaje_ganancia' => 0,
                        ]);

                        foreach ([Permissions::DASHBOARD_READ, Permissions::TICKET_READ, Permissions::USER_READ, Permissions::BONO_READ, Permissions::SORTEO_READ, Permissions::NEWS_READ] as $perm) {
                            LineAgentPermission::create([
                                'vendor_id' => $vendor->id,
                                'line_id' => $line->id,
                                'agent_id' => $member->id,
                                'permission' => $perm,
                            ]);
                        }
                    }
                }

                $lines[] = $line;
            }
        }

        $this->command->info(count($lines).' lineas creadas.');

        return $lines;
    }

    private function seedCategories(array $vendors): array
    {
        $names = ['Sorteos', 'Bonos', 'Casino online', 'Ganadores'];
        $categories = [];

        foreach ($vendors as $vendor) {
            foreach ($names as $name) {
                $categories[] = Category::withoutGlobalScopes()->updateOrCreate(
                    ['vendor_id' => $vendor->id, 'slug' => Str::slug($name).'-'.$vendor->slug],
                    ['name' => $name]
                );
            }
        }

        return $categories;
    }

    private function seedClients(array $vendors, array $roles, array $lines): array
    {
        $clientData = [
            ['Valentina', 'Rossi', 'valentina_vls', 'valentina.vls@demo.com'],
            ['Tomas', 'Medina', 'tomas_vls', 'tomas.vls@demo.com'],
            ['Camila', 'Soria', 'camila_vls', 'camila.vls@demo.com'],
            ['Lucas', 'Pereyra', 'lucas_vls', 'lucas.vls@demo.com'],
            ['Martina', 'Suarez', 'martina_vls', 'martina.vls@demo.com'],
            ['Diego', 'Correa', 'diego_vls', 'diego.vls@demo.com'],
        ];

        $clients = [];
        foreach ($clientData as $ci => $cd) {
            $vendor = $vendors[$ci % count($vendors)];
            $vendorLines = array_values(array_filter($lines, fn ($l) => (int) $l->vendor_id === (int) $vendor->id));
            $line = count($vendorLines) > 0 ? $vendorLines[$ci % count($vendorLines)] : $lines[$ci % count($lines)];

            $user = User::updateOrCreate(
                ['email' => $cd[3]],
                [
                    'name' => $cd[0],
                    'apellido' => $cd[1],
                    'username' => $cd[2],
                    'password' => Hash::make('password'),
                    'phone' => '+54911'.(50000000 + $ci),
                    'role_id' => $roles[Roles::CLIENTE]->id,
                    'vendor_id' => $vendor->id,
                    'line_id' => $line->id,
                    'status' => 'active',
                ]
            );

            $user->lines()->syncWithoutDetaching([
                $line->id => ['vendor_id' => $vendor->id, 'is_active' => true],
            ]);

            $clients[] = $user;
        }

        $this->command->info(count($clients).' clientes creados.');

        return $clients;
    }

    private function seedPosts(array $vendors, array $categories, array $lines, array $agents, array $clients): array
    {
        $templates = [
            ['Novedad: Nuevos bonos disponibles', 'Resumen: los bonos activos de la semana con beneficios exclusivos.', 'Bonos'],
            ['Ganadores del sorteo semanal', 'Resultados del sorteo con premios entregados a los ganadores.', 'Sorteos'],
            ['Tips para aprovechar tus recargas', 'Consejos para maximizar tus bonos de recarga y jugar mas.', 'Casino online'],
            ['Promocion especial de fin de semana', 'Bonus extra para jugar el sabado y domingo en todas las plataformas.', 'Bonos'],
            ['Nuevo ganador del mes', 'Felicitaciones al ganador del sorteo mensual de RED PICANTES.', 'Ganadores'],
        ];

        $posts = [];
        foreach ($vendors as $vi => $vendor) {
            $vendorLines = array_values(array_filter($lines, fn ($l) => (int) $l->vendor_id === (int) $vendor->id));
            $vendorCategories = array_values(array_filter($categories, fn ($c) => (int) $c->vendor_id === (int) $vendor->id));
            $vendorAgents = array_values(array_filter($agents, fn ($a) => (int) $a->vendor_id === (int) $vendor->id));

            foreach ($templates as $ti => $tmpl) {
                $title = $tmpl[0].' - '.$vendor->name;
                $slug = Str::slug($title).'-'.$vendor->slug;

                $cat = null;
                foreach ($vendorCategories as $vc) {
                    if ($vc->name === $tmpl[2]) {
                        $cat = $vc;
                        break;
                    }
                }

                $line = $vendorLines[$ti % count($vendorLines)];
                $author = count($vendorAgents) > 0 ? $vendorAgents[$ti % count($vendorAgents)] : null;

                $post = Post::withoutGlobalScopes()->updateOrCreate(
                    ['vendor_id' => $vendor->id, 'slug' => $slug],
                    [
                        'title' => $title,
                        'content' => '<p>'.$tmpl[1].'</p><p>Jugá con responsabilidad y consultá a tu linea de atencion ante cualquier duda.</p>',
                        'excerpt' => $tmpl[1],
                        'image' => 'https://picsum.photos/seed/'.$slug.'/900/560',
                        'status' => Post::STATUS_PUBLISHED,
                        'published_at' => Carbon::now()->subDays($ti + 1),
                        'line_id' => $line->id,
                        'category_id' => $cat?->id,
                        'author_agent_id' => $author?->id,
                    ]
                );

                // Comentario de ejemplo
                $client = $clients[($vi + $ti) % count($clients)];
                Comment::withoutGlobalScopes()->updateOrCreate(
                    ['post_id' => $post->id, 'user_id' => $client->id, 'content' => 'Excelente info, gracias por compartir!'],
                    [
                        'vendor_id' => $vendor->id,
                        'is_approved' => true,
                    ]
                );

                $posts[] = $post;
            }
        }

        $this->command->info(count($posts).' posts creados.');

        return $posts;
    }

    private function seedBonuses(array $vendors, array $lines, array $platforms, array $agents, array $clients): array
    {
        $bonusTemplates = [
            ['BIENVENIDA', 'Bono Bienvenida 50%', 50, 50000],
            ['DEPOSITO', 'Bono Deposito 100%', 100, 100000],
            ['RECARGA', 'Bono Recarga 30%', 30, 30000],
        ];

        $bonuses = [];
        foreach ($vendors as $vi => $vendor) {
            $vendorLines = array_values(array_filter($lines, fn ($l) => (int) $l->vendor_id === (int) $vendor->id));
            $vendorPlatforms = array_values(array_filter($platforms, fn ($p) => (int) $p->vendor_id === (int) $vendor->id));
            $vendorAgents = array_values(array_filter($agents, fn ($a) => (int) $a->vendor_id === (int) $vendor->id));

            foreach ($bonusTemplates as $bi => $bt) {
                $code = $bt[0].strtoupper(Str::random(4)).$vi;
                $line = $vendorLines[$bi % count($vendorLines)];
                $platform = count($vendorPlatforms) > 0 ? $vendorPlatforms[$bi % count($vendorPlatforms)] : null;
                $createdBy = count($vendorAgents) > 0 ? $vendorAgents[$bi % count($vendorAgents)]->id : null;

                $bonus = Bonus::withoutGlobalScopes()->updateOrCreate(
                    ['vendor_id' => $vendor->id, 'code' => $code],
                    [
                        'title' => $bt[1].' '.$vendor->name,
                        'description' => 'Bono del '.$bt[2].'% hasta $'.number_format($bt[3]).' en '.$vendor->name,
                        'start_date' => Carbon::now()->subDays(5),
                        'end_date' => Carbon::now()->addDays(25 - $bi),
                        'type' => 'general',
                        'status' => 'active',
                        'bonus_percent' => $bt[2],
                        'bonus_amount' => 0,
                        'min_deposit' => 1000,
                        'max_bonus' => $bt[3],
                        'total_quantity' => 200,
                        'per_user_limit' => 1,
                        'line_id' => $line->id,
                        'platform_id' => $platform?->id,
                        'created_by' => $createdBy,
                    ]
                );

                // Asignar bono a un cliente
                $client = $clients[($vi + $bi) % count($clients)];
                BonusAssignment::withoutGlobalScopes()->updateOrCreate(
                    ['bonus_id' => $bonus->id, 'user_id' => $client->id],
                    [
                        'vendor_id' => $vendor->id,
                        'status' => $bi === 0 ? 'used' : 'active',
                        'assigned_at' => Carbon::now()->subDays($bi + 1),
                        'used_at' => $bi === 0 ? Carbon::now()->subHours(5) : null,
                    ]
                );

                $bonuses[] = $bonus;
            }
        }

        $this->command->info(count($bonuses).' bonos creados.');

        return $bonuses;
    }

    private function seedRaffles(array $vendors, array $lines, array $platforms, array $clients): array
    {
        $raffles = [];

        foreach ($vendors as $vi => $vendor) {
            $vendorLines = array_values(array_filter($lines, fn ($l) => (int) $l->vendor_id === (int) $vendor->id));
            $vendorPlatforms = array_values(array_filter($platforms, fn ($p) => (int) $p->vendor_id === (int) $vendor->id));

            // Sorteo activo
            $active = Raffle::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => $vendor->id, 'title' => 'Sorteo Semanal '.$vendor->name],
                [
                    'description' => 'Participa por grandes premios con tus recargas de la semana.',
                    'status' => 'active',
                    'start_date' => Carbon::now()->subDays(3),
                    'end_date' => Carbon::now()->addHours(48),
                    'start_number' => 1,
                    'end_number' => 300,
                    'numbers_limit' => 300,
                    'line_id' => $vendorLines[0]->id,
                    'platform_id' => count($vendorPlatforms) > 0 ? $vendorPlatforms[0]->id : null,
                    'prizes' => [
                        ['position' => 1, 'name' => 'Smart TV 55"', 'amount' => 5000],
                        ['position' => 2, 'name' => 'Consola PS5', 'amount' => 3500],
                        ['position' => 3, 'name' => 'Auriculares Premium', 'amount' => 800],
                    ],
                ]
            );
            $active->lines()->sync(collect($vendorLines)->pluck('id')->mapWithKeys(fn ($id) => [$id => ['vendor_id' => $vendor->id]])->all());

            foreach ($clients as $ci => $client) {
                RaffleNumber::withoutGlobalScopes()->updateOrCreate(
                    ['raffle_id' => $active->id, 'number' => $ci + 1],
                    [
                        'vendor_id' => $vendor->id,
                        'user_id' => $client->id,
                        'line_id' => $vendorLines[$ci % count($vendorLines)]->id,
                    ]
                );
            }

            $raffles[] = $active;

            // Sorteo finalizado
            $finished = Raffle::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => $vendor->id, 'title' => 'Sorteo Anterior '.$vendor->name],
                [
                    'description' => 'Resultados del sorteo anterior con premios entregados.',
                    'status' => 'finished',
                    'start_date' => Carbon::now()->subDays(30),
                    'end_date' => Carbon::now()->subDays(2),
                    'start_number' => 1,
                    'end_number' => 200,
                    'numbers_limit' => 200,
                    'winner_user_id' => $clients[$vi % count($clients)]->id,
                    'winner_number' => $vi + 7,
                    'line_id' => $vendorLines[1]->id,
                    'platform_id' => count($vendorPlatforms) > 1 ? $vendorPlatforms[1]->id : null,
                    'prizes' => [
                        ['position' => 1, 'name' => 'Premio en efectivo', 'amount' => 2500],
                    ],
                ]
            );
            $finished->lines()->sync([$vendorLines[1]->id => ['vendor_id' => $vendor->id]]);

            $raffles[] = $finished;
        }

        $this->command->info(count($raffles).' sorteos creados.');

        return $raffles;
    }

    private function seedSales(array $vendors, array $lines, array $agents, array $clients, array $platforms): void
    {
        $count = 0;
        foreach ($vendors as $vi => $vendor) {
            $vendorLines = array_values(array_filter($lines, fn ($l) => (int) $l->vendor_id === (int) $vendor->id));
            $vendorAgents = array_values(array_filter($agents, fn ($a) => (int) $a->vendor_id === (int) $vendor->id));
            $vendorPlatforms = array_values(array_filter($platforms, fn ($p) => (int) $p->vendor_id === (int) $vendor->id));

            foreach (range(0, 9) as $si) {
                $line = $vendorLines[$si % count($vendorLines)];
                $agent = count($vendorAgents) > 0 ? $vendorAgents[$si % count($vendorAgents)] : null;
                $client = $clients[$si % count($clients)];
                $platform = count($vendorPlatforms) > 0 ? $vendorPlatforms[$si % count($vendorPlatforms)] : null;

                Sale::withoutGlobalScopes()->create([
                    'vendor_id' => $vendor->id,
                    'line_id' => $line->id,
                    'agent_id' => $agent?->id,
                    'client_id' => $client->id,
                    'platform_id' => $platform?->id,
                    'fecha_inicio' => Carbon::now()->subDays(15 - $si),
                    'fecha_fin' => Carbon::now()->subDays(15 - $si),
                    'descripcion' => 'Venta demo '.$vendor->name,
                    'monto_fichas' => random_int(10000, 180000),
                    'ganancia_superagente' => random_int(1500, 35000),
                ]);
                $count++;
            }
        }

        $this->command->info($count.' ventas creadas.');
    }

    private function seedCarousel(array $vendors, array $lines): void
    {
        $count = 0;
        foreach ($vendors as $vi => $vendor) {
            $vendorLines = array_values(array_filter($lines, fn ($l) => (int) $l->vendor_id === (int) $vendor->id));

            foreach ([
                ['Bienvenido a '.$vendor->name, '/'.$vendor->slug],
                ['Bonos activos en '.$vendor->name, '/'.$vendor->slug.'/bonos'],
                ['Sorteo '.$vendor->name, '/'.$vendor->slug.'/sorteos'],
            ] as $ci => $item) {
                CarouselItem::updateOrCreate(
                    ['title' => $item[0], 'vendor_id' => $vendor->id],
                    [
                        'image' => 'https://picsum.photos/seed/'.$vendor->slug.'-carousel-'.$ci.'/1600/680',
                        'link' => $item[1],
                        'order' => $ci + 1,
                        'line_id' => $vendorLines[$ci % count($vendorLines)]->id,
                    ]
                );
                $count++;
            }
        }

        $this->command->info($count.' items de carrusel creados.');
    }

    private function seedHomeSections(array $vendors, array $lines, array $bonuses, array $raffles, array $posts): void
    {
        $allPostIds = collect($posts)->pluck('id')->toArray();

        foreach ($vendors as $vendor) {
            $vendorLineIds = collect($lines)->filter(fn ($l) => (int) $l->vendor_id === (int) $vendor->id)->pluck('id')->values()->toArray();
            $vendorBonusIds = collect($bonuses)->filter(fn ($b) => (int) $b->vendor_id === (int) $vendor->id)->pluck('id')->values()->toArray();
            $vendorRaffleIds = collect($raffles)->filter(fn ($r) => (int) $r->vendor_id === (int) $vendor->id)->pluck('id')->values()->toArray();
            $vendorPostIds = collect($posts)->filter(fn ($p) => (int) $p->vendor_id === (int) $vendor->id)->pluck('id')->values()->toArray();
            $postIds = ! empty($vendorPostIds) ? $vendorPostIds : array_slice($allPostIds, 0, 6);

            $sections = [
                ['section_key' => 'como-empezar', 'order' => 0, 'enabled' => true, 'kicker' => 'Cómo funciona', 'title' => 'Empezá con', 'highlight' => $vendor->name, 'subtitle' => 'Contactanos, pedí tu usuario y empezá a jugar en minutos.', 'repeater_data' => [['title' => 'Contactanos', 'subtitle' => 'Escribinos por WhatsApp o Telegram y pedí tu acceso.'], ['title' => 'Cargá saldo', 'subtitle' => 'Acreditamos al instante con los medios disponibles.'], ['title' => 'Jugá', 'subtitle' => 'Entrá a la plataforma y disfrutá el casino online.']]],
                ['section_key' => 'lineas', 'order' => 1, 'enabled' => ! empty($vendorLineIds), 'kicker' => 'Líneas activas', 'title' => 'Nuestras', 'highlight' => 'líneas', 'subtitle' => 'Líneas con soporte directo, cargas rápidas y seguimiento de cada operación.', 'line_ids' => $vendorLineIds],
                ['section_key' => 'sorteo', 'order' => 2, 'enabled' => ! empty($vendorRaffleIds), 'kicker' => 'Sorteos exclusivos', 'title' => 'Sorteos de', 'highlight' => $vendor->name, 'subtitle' => 'Participá en nuestros sorteos y ganá premios reales.', 'raffle_type' => 'active', 'raffle_ids' => $vendorRaffleIds],
                ['section_key' => 'nosotros', 'order' => 3, 'enabled' => true, 'kicker' => 'Sobre '.$vendor->name, 'title' => 'Por qué elegirnos', 'highlight' => '', 'subtitle' => $vendor->description ?? '', 'repeater_data' => $vendor->features ?? []],
                ['section_key' => 'bonos', 'order' => 4, 'enabled' => ! empty($vendorBonusIds), 'kicker' => 'Bonos exclusivos', 'title' => 'Bonos de', 'highlight' => $vendor->name, 'subtitle' => 'Bonos activos para arrancar con ventaja.', 'action_text' => 'Ver todos', 'action_url' => '/'.$vendor->slug.'/bonos', 'bonus_type' => 'active', 'bonus_ids' => $vendorBonusIds],
                ['section_key' => 'blog', 'order' => 5, 'enabled' => true, 'kicker' => 'Novedades', 'title' => 'Últimas', 'highlight' => 'novedades', 'subtitle' => 'Enterate de sorteos, bonos y noticias del casino.', 'action_text' => 'Ver novedades', 'action_url' => '/'.$vendor->slug.'/blog', 'post_ids' => $postIds],
            ];

            foreach ($sections as $data) {
                HomeSection::withoutGlobalScopes()->updateOrCreate(
                    ['vendor_id' => $vendor->id, 'section_key' => $data['section_key']],
                    [
                        'order' => $data['order'],
                        'enabled' => $data['enabled'],
                        'kicker' => $data['kicker'],
                        'title' => $data['title'],
                        'highlight' => $data['highlight'],
                        'subtitle' => $data['subtitle'] ?? null,
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
                    ]
                );
            }
        }

        $this->command->info(count($vendors).' vendors con HomeSections pobladas.');
    }

    private function seedRatings(array $vendors, array $lines, array $clients): void
    {
        $count = 0;
        foreach ($vendors as $vi => $vendor) {
            $vendorLines = array_values(array_filter($lines, fn ($l) => (int) $l->vendor_id === (int) $vendor->id));

            foreach ($vendorLines as $li => $line) {
                $client = $clients[($vi + $li) % count($clients)];
                LineRating::withoutGlobalScopes()->updateOrCreate(
                    ['line_id' => $line->id, 'user_id' => $client->id],
                    [
                        'vendor_id' => $vendor->id,
                        'rating' => random_int(4, 5),
                        'message' => 'Excelente atencion y plataformas de calidad.',
                    ]
                );
                $count++;
            }
        }

        $this->command->info($count.' valoraciones creadas.');
    }
}
