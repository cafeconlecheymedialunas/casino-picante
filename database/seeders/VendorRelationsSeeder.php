<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Bonus;
use App\Models\HomeSection;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Platform;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Support\LineRoles;
use App\Support\Roles;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorRelationsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Asegurar plataformas ──
        $platformNames = ['WhatsApp', 'Telegram', 'Web', 'Betsson', 'Codere', '1xBet', 'Bet365', 'Sportingbet'];
        $platforms = [];
        foreach ($platformNames as $name) {
            $platforms[] = Platform::updateOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'is_active' => true]
            );
        }

        // ── Asegurar vendors con datos completos ──
        $roleCajero = Role::firstOrCreate(['name' => Roles::CAJERO], ['display_name' => 'Cajero']);
        $roleAgent = Role::firstOrCreate(['name' => Roles::AGENTE], ['display_name' => 'Agente']);

        $vendorDefs = [
            [
                'slug' => 'betmaster',
                'name' => 'BetMaster',
                'description' => 'El cajero mas confiable de la red. Pagos al instante, atencion 24/7 y las mejores lineas del mercado.',
                'logo' => 'https://picsum.photos/id/106/200/200',
                'hero' => 'https://picsum.photos/id/160/1200/400',
                'portrait' => 'https://picsum.photos/id/201/300/400',
                'contacts' => [
                    ['type' => 'whatsapp', 'name' => 'WhatsApp principal', 'value' => '+5491155550001'],
                    ['type' => 'telegram',  'name' => 'Telegram',          'value' => '@betmaster_oficial'],
                    ['type' => 'instagram', 'name' => 'Instagram',         'value' => '@betmaster'],
                ],
                'features' => [
                    ['icon' => 'fa-solid fa-bolt',     'title' => 'Pagos en minutos', 'description' => 'Cargas y retiros en menos de 5 minutos.'],
                    ['icon' => 'fa-solid fa-shield',   'title' => '100% Seguro',      'description' => 'Operacion verificada y transparente.'],
                    ['icon' => 'fa-solid fa-headset',  'title' => 'Soporte 24/7',     'description' => 'Siempre disponible para ayudarte.'],
                ],
                'lines' => [
                    ['name' => 'Linea Clasica BetMaster',  'phone' => '+5491155550011'],
                    ['name' => 'Linea Premium BetMaster',  'phone' => '+5491155550012'],
                    ['name' => 'Linea Rapida BetMaster',   'phone' => '+5491155550013'],
                    ['name' => 'Platinum Club',             'phone' => '+5491155550014'],
                ],
            ],
            [
                'slug' => 'casino-royale',
                'name' => 'Casino Royale',
                'description' => 'Experiencia premium de casino. Bonos exclusivos y acceso a las mejores plataformas.',
                'logo' => 'https://picsum.photos/id/1011/200/200',
                'hero' => 'https://picsum.photos/id/1015/1200/400',
                'portrait' => 'https://picsum.photos/id/1005/300/400',
                'contacts' => [
                    ['type' => 'whatsapp', 'name' => 'Contacto principal', 'value' => '+5491155550002'],
                    ['type' => 'telegram', 'name' => 'Telegram',           'value' => '@casinoroyale_arg'],
                ],
                'features' => [
                    ['icon' => 'fa-solid fa-crown',    'title' => 'Experiencia VIP',  'description' => 'Servicio exclusivo para cada jugador.'],
                    ['icon' => 'fa-solid fa-gift',     'title' => 'Bonos diarios',    'description' => 'Promociones todos los dias.'],
                    ['icon' => 'fa-solid fa-trophy',   'title' => 'Torneos',          'description' => 'Participa en sorteos semanales.'],
                ],
                'lines' => [
                    ['name' => 'VIP Casino',   'phone' => '+5491155550021'],
                    ['name' => 'Gold Sports',  'phone' => '+5491155550022'],
                    ['name' => 'Silver Line',  'phone' => '+5491155550023'],
                ],
            ],
            [
                'slug' => 'lucky-spin',
                'name' => 'Lucky Spin',
                'description' => 'La suerte esta de tu lado. Sorteos diarios y los mejores bonos de recarga.',
                'logo' => 'https://picsum.photos/id/133/200/200',
                'hero' => 'https://picsum.photos/id/251/1200/400',
                'portrait' => 'https://picsum.photos/id/29/300/400',
                'contacts' => [
                    ['type' => 'whatsapp', 'name' => 'WhatsApp', 'value' => '+5491155550003'],
                ],
                'features' => [
                    ['icon' => 'fa-solid fa-dice',     'title' => 'Sorteos diarios',  'description' => 'Gana premios todos los dias.'],
                    ['icon' => 'fa-solid fa-percent',  'title' => 'Mejores bonos',    'description' => 'Hasta 200% en tu primer deposito.'],
                    ['icon' => 'fa-solid fa-star',     'title' => 'Programa VIP',     'description' => 'Acumula puntos y sube de nivel.'],
                ],
                'lines' => [
                    ['name' => 'Elite Bet',    'phone' => '+5491155550031'],
                    ['name' => 'Lucky Gold',   'phone' => '+5491155550032'],
                ],
            ],
        ];

        $vendorMap = [];
        foreach ($vendorDefs as $i => $def) {
            $cajeroUser = User::updateOrCreate(
                ['email' => 'cajero_'.$def['slug'].'@demo.com'],
                [
                    'name' => $def['name'],
                    'password' => bcrypt('demo123'),
                    'username' => Str::slug($def['slug']),
                    'phone' => '+549115555'.str_pad($i, 4, '0', STR_PAD_LEFT),
                    'role_id' => $roleCajero->id,
                    'status' => 'active',
                ]
            );

            $vendor = Vendor::updateOrCreate(
                ['slug' => $def['slug']],
                [
                    'user_id' => $cajeroUser->id,
                    'name' => $def['name'],
                    'description' => $def['description'],
                    'logo' => $def['logo'],
                    'hero_image' => $def['hero'],
                    'portrait_image' => $def['portrait'],
                    'contacts' => $def['contacts'],
                    'features' => $def['features'],
                    'is_active' => true,
                ]
            );

            $vendorMap[$def['slug']] = ['vendor' => $vendor, 'lines' => []];

            // ── Líneas del vendor ──
            foreach ($def['lines'] as $j => $ld) {
                $line = Line::updateOrCreate(
                    ['name' => $ld['name'], 'vendor_id' => $vendor->id],
                    [
                        'vendor_id' => $vendor->id,
                        'type' => 'whatsapp',
                        'phone' => $ld['phone'],
                        'portada_url' => 'https://picsum.photos/id/'.($i * 10 + $j + 20).'/800/400',
                        'perfil_url' => 'https://picsum.photos/id/'.($i * 10 + $j + 50).'/300/300',
                        'status' => 'active',
                        'contact_links' => [['type' => 'whatsapp', 'value' => $ld['phone'], 'name' => 'WhatsApp']],
                    ]
                );
                $vendorMap[$def['slug']]['lines'][] = $line;

                // ── Plataformas asignadas a la línea ──
                $assignedPlatforms = array_slice($platforms, $j % 3, 3);
                foreach ($assignedPlatforms as $platform) {
                    DB::table('line_platform')->updateOrInsert(
                        ['line_id' => $line->id, 'platform_id' => $platform->id],
                        [
                            'vendor_id' => $vendor->id,
                            'is_active' => true,
                            'custom_message' => 'Acceso exclusivo '.$def['name'].' — '.$platform->name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
        $this->command->info('Vendors, líneas y plataformas listos.');

        // ── Agentes por vendor ──
        $agentDefs = [
            ['name' => 'Roberto Silva',   'username' => 'rsilva',   'email' => 'rsilva@demo.com'],
            ['name' => 'Lucia Torres',    'username' => 'ltorres',  'email' => 'ltorres@demo.com'],
            ['name' => 'Matias Gomez',    'username' => 'mgomez',   'email' => 'mgomez@demo.com'],
            ['name' => 'Florencia Paz',   'username' => 'fpaz',     'email' => 'fpaz@demo.com'],
            ['name' => 'Nicolas Vera',    'username' => 'nvera',    'email' => 'nvera@demo.com'],
            ['name' => 'Antonella Cruz',  'username' => 'acruz',    'email' => 'acruz@demo.com'],
        ];

        $allPerms = LineAgentPermission::allPermissions();
        $agentIdx = 0;
        foreach ($vendorMap as $slug => $vm) {
            $vendor = $vm['vendor'];
            $lines = $vm['lines'];

            foreach ($lines as $lineIdx => $line) {
                $ad = $agentDefs[$agentIdx % count($agentDefs)];
                $agentUser = User::updateOrCreate(
                    ['email' => $ad['email']],
                    [
                        'name' => explode(' ', $ad['name'])[0],
                        'apellido' => explode(' ', $ad['name'])[1] ?? '',
                        'password' => bcrypt('demo123'),
                        'username' => $ad['username'],
                        'phone' => '+549119900'.str_pad($agentIdx, 4, '0', STR_PAD_LEFT),
                        'role_id' => $roleAgent->id,
                        'status' => 'active',
                    ]
                );
                $agent = Agent::updateOrCreate(
                    ['email' => $ad['email']],
                    [
                        'user_id' => $agentUser->id,
                        'name' => $ad['name'],
                        'username' => $ad['username'],
                        'password' => bcrypt('demo123'),
                        'status' => 'active',
                    ]
                );
                $la = LineAgent::firstOrCreate(
                    ['line_id' => $line->id, 'agent_id' => $agent->id],
                    ['role' => $lineIdx === 0 ? LineRoles::ENCARGADO : LineRoles::MIEMBRO, 'is_active' => true]
                );
                foreach ($allPerms as $perm) {
                    LineAgentPermission::firstOrCreate([
                        'line_id' => $la->line_id,
                        'agent_id' => $la->agent_id,
                        'permission' => $perm,
                    ]);
                }
                $agentIdx++;
            }
        }
        $this->command->info('Agentes asignados a líneas.');

        // ── Bonos por vendor ──
        $bonusDefs = [
            ['code' => 'BIENVENIDA50',  'title' => 'Bono Bienvenida 50%',  'percent' => 50,  'max' => 50000,  'days' => 60],
            ['code' => 'DEPOSITO100',   'title' => 'Bono Depósito 100%',   'percent' => 100, 'max' => 100000, 'days' => 45],
            ['code' => 'RECARGA30',     'title' => 'Bono Recarga 30%',     'percent' => 30,  'max' => 30000,  'days' => 30],
            ['code' => 'VIP200',        'title' => 'Bono VIP 200%',        'percent' => 200, 'max' => 200000, 'days' => 90],
            ['code' => 'FINDE15',       'title' => 'Especial Fin de Semana 15%', 'percent' => 15, 'max' => 15000, 'days' => 7],
        ];

        foreach ($vendorMap as $slug => $vm) {
            $vendor = $vm['vendor'];
            $lines = $vm['lines'];

            foreach ($bonusDefs as $k => $bd) {
                $line = $lines[$k % count($lines)];
                Bonus::updateOrCreate(
                    ['code' => $bd['code'].'_'.$vendor->id, 'vendor_id' => $vendor->id],
                    [
                        'title' => $bd['title'],
                        'description' => 'Bono del '.$bd['percent'].'% hasta $'.number_format($bd['max']).'. Aplica en '.$line->name.'. Consulta T&C.',
                        'start_date' => Carbon::now()->subDays(5),
                        'end_date' => Carbon::now()->addDays($bd['days']),
                        'bonus_percent' => $bd['percent'],
                        'max_bonus' => $bd['max'],
                        'status' => 'active',
                        'vendor_id' => $vendor->id,
                        'line_id' => $line->id,
                        'platform_id' => $platforms[$k % count($platforms)]->id,
                        'user_id' => $vendor->user_id,
                        'slug' => Str::slug($bd['title'].'-'.$vendor->slug).'-'.uniqid(),
                    ]
                );
            }
        }
        $this->command->info('Bonos listos.');

        // ── Sorteos por vendor ──
        $raffleDefs = [
            ['title' => 'Sorteo Semanal',    'end' => 500, 'addDays' => 5],
            ['title' => 'Sorteo Mensual VIP', 'end' => 200, 'addDays' => 20],
            ['title' => 'Gran Premio',       'end' => 100, 'addDays' => 10],
        ];

        foreach ($vendorMap as $slug => $vm) {
            $vendor = $vm['vendor'];
            $lines = $vm['lines'];

            foreach ($raffleDefs as $k => $rd) {
                $line = $lines[$k % count($lines)];
                $raffle = Raffle::updateOrCreate(
                    ['title' => $rd['title'].' - '.$vendor->name, 'vendor_id' => $vendor->id],
                    [
                        'vendor_id' => $vendor->id,
                        'line_id' => $line->id,
                        'platform_id' => $platforms[$k % count($platforms)]->id,
                        'status' => 'active',
                        'start_date' => Carbon::now()->subDays(3),
                        'end_date' => Carbon::now()->addDays($rd['addDays']),
                        'start_number' => 1,
                        'end_number' => $rd['end'],
                        'prizes' => [['position' => 1, 'name' => 'Primer premio', 'amount' => rand(10000, 100000), 'image' => 'https://picsum.photos/id/1049/480/260']],
                        'slug' => Str::slug($rd['title'].'-'.$vendor->slug).'-'.uniqid(),
                    ]
                );

                // Números de sorteo
                for ($n = 0; $n < 5; $n++) {
                    $demoUser = User::where('status', 'active')->skip($n)->first();
                    if ($demoUser) {
                        RaffleNumber::firstOrCreate(
                            ['raffle_id' => $raffle->id, 'user_id' => $demoUser->id],
                            ['number' => rand(1, $rd['end'])]
                        );
                    }
                }
            }
        }
        $this->command->info('Sorteos listos.');

        // ── Posts por vendor ──
        $postTitles = [
            'Nuevos bonos de bienvenida disponibles este mes',
            'Resultado del sorteo semanal — felicitamos al ganador',
            'Actualizacion de plataformas disponibles',
            'Promocion especial de fin de semana',
        ];

        foreach ($vendorMap as $slug => $vm) {
            $vendor = $vm['vendor'];
            $lines = $vm['lines'];

            foreach ($postTitles as $k => $title) {
                $line = $lines[$k % count($lines)];
                Post::firstOrCreate(
                    ['title' => $title.' | '.$vendor->name],
                    [
                        'slug' => Str::slug($title.'-'.$vendor->slug).'-'.uniqid(),
                        'content' => '<p>'.fake()->paragraphs(3, true).'</p>',
                        'excerpt' => fake()->sentence(12),
                        'status' => Post::STATUS_PUBLISHED,
                        'published_at' => Carbon::now()->subDays($k + 1),
                        'vendor_id' => $vendor->id,
                        'line_id' => $line->id,
                    ]
                );
            }
        }
        $this->command->info('Posts listos.');

        // ── HomeSections por vendor ──
        foreach ($vendorMap as $slug => $vm) {
            $vendor = $vm['vendor'];
            $vLines = $vm['lines'];

            $vendorBonusIds = Bonus::withoutGlobalScopes()->where('vendor_id', $vendor->id)->pluck('id')->toArray();
            $vendorRaffleIds = Raffle::withoutGlobalScopes()->where('vendor_id', $vendor->id)->where('status', 'active')->pluck('id')->toArray();
            $vendorPostIds = Post::withoutGlobalScopes()->where('vendor_id', $vendor->id)->where('status', Post::STATUS_PUBLISHED)->pluck('id')->toArray();
            $vendorLineIds = collect($vLines)->pluck('id')->toArray();

            $sections = [
                ['section_key' => 'como-empezar', 'order' => 0, 'enabled' => true, 'kicker' => 'Cómo funciona', 'title' => 'Empezá con', 'highlight' => $vendor->name, 'subtitle' => 'Contactanos, pedí tu usuario y empezá a jugar en minutos.', 'repeater_data' => [['title' => 'Contactanos', 'subtitle' => 'Escribinos por WhatsApp o Telegram.'], ['title' => 'Cargá saldo', 'subtitle' => 'Acreditamos al instante con los medios disponibles.'], ['title' => 'Jugá', 'subtitle' => 'Entrá a la plataforma y disfrutá.']]],
                ['section_key' => 'lineas', 'order' => 1, 'enabled' => ! empty($vendorLineIds), 'kicker' => 'Líneas activas', 'title' => 'Nuestras', 'highlight' => 'líneas', 'subtitle' => 'Líneas de atención directa con soporte y cargas rápidas.', 'line_ids' => $vendorLineIds],
                ['section_key' => 'sorteo', 'order' => 2, 'enabled' => ! empty($vendorRaffleIds), 'kicker' => 'Sorteos exclusivos', 'title' => 'Sorteos de', 'highlight' => $vendor->name, 'subtitle' => 'Participá y ganá premios reales.', 'raffle_type' => 'active', 'raffle_ids' => $vendorRaffleIds],
                ['section_key' => 'nosotros', 'order' => 3, 'enabled' => true, 'kicker' => 'Sobre '.$vendor->name, 'title' => 'Por qué elegirnos', 'highlight' => '', 'subtitle' => $vendor->description ?? '', 'repeater_data' => $vendor->features ?? []],
                ['section_key' => 'bonos', 'order' => 4, 'enabled' => ! empty($vendorBonusIds), 'kicker' => 'Bonos exclusivos', 'title' => 'Bonos de', 'highlight' => $vendor->name, 'subtitle' => 'Bonos activos para arrancar con ventaja.', 'action_text' => 'Ver todos', 'action_url' => '/'.$vendor->slug.'/bonos', 'bonus_type' => 'active', 'bonus_ids' => $vendorBonusIds],
                ['section_key' => 'blog', 'order' => 5, 'enabled' => true, 'kicker' => 'Novedades', 'title' => 'Últimas', 'highlight' => 'novedades', 'subtitle' => 'Enterate de sorteos, bonos y noticias.', 'action_text' => 'Ver novedades', 'action_url' => '/'.$vendor->slug.'/blog', 'post_ids' => ! empty($vendorPostIds) ? $vendorPostIds : []],
            ];

            foreach ($sections as $data) {
                HomeSection::withoutGlobalScopes()->updateOrCreate(
                    ['vendor_id' => $vendor->id, 'section_key' => $data['section_key']],
                    array_merge($data, ['vendor_id' => $vendor->id, 'content' => null])
                );
            }
        }
        $this->command->info('HomeSections por vendor listas.');

        $this->command->info('======================================');
        $this->command->info(' RELACIONES DE VENDOR CARGADAS');
        $this->command->info('======================================');
        foreach ($vendorMap as $slug => $vm) {
            $v = $vm['vendor'];
            $this->command->info(' /'.$slug.' → '.$v->name.' ('.count($vm['lines']).' lineas)');
        }
        $this->command->info('======================================');
    }
}
