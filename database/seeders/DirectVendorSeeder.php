<?php

namespace Database\Seeders;

use App\Models\Bonus;
use App\Models\HomeSection;
use App\Models\Line;
use App\Models\Platform;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\User;
use App\Models\Vendor;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DirectVendorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('name', Roles::ADMIN))->first()
            ?? User::first();

        $platforms = Platform::withoutGlobalScopes()->pluck('id', 'name');

        // ── Cajero Oficial ──────────────────────────────────────────────────
        $vendor = Vendor::updateOrCreate(
            ['slug' => 'cajero-oficial'],
            [
                'user_id'      => $admin?->id,
                'name'         => 'Cajero Oficial',
                'description'  => 'El cajero directo de la red. Cargas instantáneas, atención personalizada y los mejores bonos exclusivos para nuestros usuarios. Sin intermediarios.',
                'is_active'    => true,
                'is_direct'    => true,
                'logo'         => 'https://picsum.photos/id/1025/256/256',
                'hero_image'   => 'https://picsum.photos/id/1018/1400/500',
                'portrait_image' => 'https://picsum.photos/id/1016/600/800',
                'contacts'     => [
                    ['name' => 'WhatsApp',  'type' => 'whatsapp',  'value' => '5491100000000'],
                    ['name' => 'Telegram',  'type' => 'telegram',  'value' => 'https://t.me/cajerooficial'],
                    ['name' => 'Instagram', 'type' => 'instagram', 'value' => 'https://instagram.com/cajerooficial'],
                ],
                'features' => [
                    ['icon' => 'fa-solid fa-crown',          'title' => 'Red Oficial',    'desc' => 'Operado directamente por la red. Sin intermediarios.'],
                    ['icon' => 'fa-solid fa-bolt',           'title' => 'Carga Express',  'desc' => 'Saldo acreditado en menos de 2 minutos.'],
                    ['icon' => 'fa-solid fa-shield-halved',  'title' => '100% Seguro',    'desc' => 'Transacciones verificadas y protegidas.'],
                    ['icon' => 'fa-solid fa-gift',           'title' => 'Bonos VIP',      'desc' => 'Accedé a bonos exclusivos solo para líneas directas.'],
                    ['icon' => 'fa-solid fa-headset',        'title' => 'Soporte 24/7',   'desc' => 'Asistencia inmediata todos los días del año.'],
                    ['icon' => 'fa-solid fa-star',           'title' => 'Programa VIP',   'desc' => 'Acumulá puntos y subí de nivel con cada carga.'],
                ],
            ]
        );

        // ── Lines ────────────────────────────────────────────────────────────
        $platformIds = [
            $platforms['VIP Casino']   ?? null,
            $platforms['Golden Bet']   ?? null,
            $platforms['Hybrid Club']  ?? null,
            $platforms['Etoile Play']  ?? null,
        ];

        $lineNames = ['VIP Casino', 'Golden Bet', 'Hybrid Club', 'Etoile Play'];

        $lineIds = [];
        foreach ($lineNames as $i => $name) {
            $pid = $platformIds[$i] ?? null;
            $line = Line::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => $vendor->id, 'name' => $name . ' — Oficial'],
                [
                    'description' => 'Línea directa ' . $name . ' administrada por el cajero oficial de la red.',
                    'status'      => 'active',
                    'platforms'   => $pid ? [$pid] : [],
                    'perfil_url'  => 'https://picsum.photos/id/' . (1030 + $i) . '/256/256',
                ]
            );
            $lineIds[] = $line->id;
        }

        // ── Bonuses ──────────────────────────────────────────────────────────
        $firstLineId  = $lineIds[0] ?? null;
        $firstPlatId  = $platformIds[0] ?? null;

        $bonusData = [
            [
                'code'           => 'BIENVENIDA-OFICIAL',
                'title'          => 'Bono de Bienvenida Oficial',
                'description'    => '100% de bonificación en tu primer depósito hasta $10.000. Solo para líneas directas.',
                'type'           => 'welcome',
                'bonus_amount'   => 10000,
                'bonus_percent'  => 100,
                'min_deposit'    => 1000,
                'max_bonus'      => 10000,
                'total_quantity' => 500,
                'per_user_limit' => 1,
                'status'         => 'active',
                'start_date'     => now(),
                'end_date'       => now()->addYear(),
            ],
            [
                'code'           => 'RECARGA-VIP',
                'title'          => 'Recarga Semanal VIP',
                'description'    => '30% extra en cada recarga los fines de semana. Exclusivo cajero oficial.',
                'type'           => 'reload',
                'bonus_amount'   => 5000,
                'bonus_percent'  => 30,
                'min_deposit'    => 500,
                'max_bonus'      => 5000,
                'total_quantity' => 1000,
                'per_user_limit' => 4,
                'status'         => 'active',
                'start_date'     => now(),
                'end_date'       => now()->addYear(),
            ],
            [
                'code'           => 'FREESPINS50',
                'title'          => 'Free Spins Pack 50',
                'description'    => '50 giros gratis en los mejores slots. Sin depósito mínimo requerido.',
                'type'           => 'freespins',
                'bonus_amount'   => 0,
                'bonus_percent'  => 0,
                'min_deposit'    => 0,
                'max_bonus'      => 0,
                'total_quantity' => 300,
                'per_user_limit' => 1,
                'status'         => 'active',
                'start_date'     => now(),
                'end_date'       => now()->addYear(),
            ],
        ];

        $bonusIds = [];
        foreach ($bonusData as $bd) {
            $bonus = Bonus::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => $vendor->id, 'title' => $bd['title']],
                array_merge($bd, [
                    'vendor_id'  => $vendor->id,
                    'slug'       => Str::slug($bd['title']),
                    'user_id'    => null,
                    'created_by' => $admin?->id,
                    'line_id'    => $firstLineId,
                    'platform_id' => $firstPlatId,
                ])
            );
            $bonusIds[] = $bonus->id;
        }

        // ── Raffles ──────────────────────────────────────────────────────────
        $raffle = Raffle::withoutGlobalScopes()->updateOrCreate(
            ['title' => 'SORTEO EXCLUSIVO CAJERO OFICIAL'],
            [
                'vendor_id'      => $vendor->id,
                'slug'           => 'sorteo-exclusivo-cajero-oficial',
                'description'    => 'Participá cargando saldo por el cajero oficial. Cada $500 cargados = 1 número.',
                'status'         => 'active',
                'start_date'     => now()->subDays(1),
                'end_date'       => now()->addDays(14),
                'start_number'   => 100,
                'end_number'     => 2000,
                'numbers_limit'  => 1900,
                'winner_user_id' => null,
                'winner_number'  => null,
                'line_id'        => $lineIds[0] ?? null,
                'platform_id'    => $firstPlatId,
                'prizes'         => [
                    ['position' => 1, 'name' => 'Smart TV 65" 4K', 'amount' => 2200,  'image' => 'https://picsum.photos/id/1050/480/260'],
                    ['position' => 2, 'name' => 'iPhone 16 Pro',   'amount' => 1500,  'image' => 'https://picsum.photos/id/1060/480/260'],
                    ['position' => 3, 'name' => 'Créditos $20.000','amount' => 20000, 'image' => 'https://picsum.photos/id/1070/480/260'],
                ],
            ]
        );
        $raffle->lines()->sync($lineIds);
        $raffleIds = [$raffle->id];

        $this->command->info('DirectVendorSeeder: Cajero Oficial creado con lineas, bonos y sorteo.');
    }
}

