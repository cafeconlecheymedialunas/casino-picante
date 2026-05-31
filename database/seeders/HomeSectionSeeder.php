<?php

namespace Database\Seeders;

use App\Models\Bonus;
use App\Models\CarouselItem;
use App\Models\HomeConfig;
use App\Models\HomeSection;
use App\Models\Line;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        // IDs de vendors directos (Cajero Oficial y similares)
        $directVendorIds = Vendor::where('is_direct', true)->pluck('id');

        // Home sections: contenido del cajero oficial prioritario
        $lineIds = Line::withoutGlobalScopes()
            ->where('status', 'active')
            ->whereIn('vendor_id', $directVendorIds)
            ->orderBy('name')
            ->take(6)
            ->pluck('id')
            ->toArray();

        $raffleIds = Raffle::withoutGlobalScopes()
            ->whereIn('status', ['active', 'inactive'])
            ->whereIn('vendor_id', $directVendorIds)
            ->where('end_date', '>=', now())
            ->orderBy('end_date')
            ->take(5)
            ->pluck('id')
            ->toArray();

        $bonusIds = Bonus::withoutGlobalScopes()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->whereIn('vendor_id', $directVendorIds)
            ->orderBy('start_date', 'desc')
            ->take(6)
            ->pluck('id')
            ->toArray();

        $postIds = Post::withoutGlobalScopes()
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->pluck('id')
            ->toArray();

        $this->seedCarousel();
        $this->seedSections($lineIds, $raffleIds, $bonusIds, $postIds);
        $this->seedPublicPages();

        $this->command?->info('Home global poblada correctamente.');
    }

    private function seedCarousel(): void
    {
        $items = [
            [
                'title' => 'Casino online con atencion real',
                'description' => 'Elegi una linea activa, pedi tu usuario y empeza a jugar con soporte humano desde el primer mensaje.',
                'cta_text' => 'Ver lineas',
                'image' => 'https://picsum.photos/id/1057/1600/680',
                'link' => '/lineas',
            ],
            [
                'title' => 'Bonos activos para jugar mas',
                'description' => 'Promociones vigentes, recargas con ventaja y beneficios seleccionados para arrancar mejor.',
                'cta_text' => 'Explorar bonos',
                'image' => 'https://picsum.photos/id/1058/1600/680',
                'link' => '/bonos',
            ],
            [
                'title' => 'Sorteos con premios reales',
                'description' => 'Participa en sorteos activos, revisa premios disponibles y segui las fechas desde la home.',
                'cta_text' => 'Ver sorteos',
                'image' => 'https://picsum.photos/id/1059/1600/680',
                'link' => '/sorteos',
            ],
        ];

        foreach ($items as $order => $data) {
            $item = CarouselItem::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => null, 'title' => $data['title']],
                [
                    'vendor_id' => null,
                    'image' => $data['image'],
                    'description' => $data['description'],
                    'cta_text' => $data['cta_text'],
                    'link' => $data['link'],
                    'order' => $order + 1,
                    'line_id' => null,
                ]
            );

            HomeConfig::withoutGlobalScopes()->updateOrCreate(
                [
                    'vendor_id' => null,
                    'section' => HomeConfig::SECTION_CAROUSEL,
                    'item_id' => $item->id,
                ],
                [
                    'vendor_id' => null,
                    'order' => $order,
                ]
            );
        }
    }

    private function seedSections(array $lineIds, array $raffleIds, array $bonusIds, array $postIds): void
    {
        $sections = [
            [
                'section_key'  => 'como-empezar',
                'order'        => 0,
                'enabled'      => true,
                'kicker'       => 'Como funciona',
                'title'        => 'Empeza en',
                'highlight'    => '3 pasos',
                'subtitle'     => 'Sin vueltas: contacto, carga y juego. Si necesitas ayuda, una persona te responde.',
                'content'      => '',
                'image'        => null,
                'action_text'  => '',
                'action_url'   => '',
                'line_ids'     => [],
                'raffle_type'  => null,
                'raffle_ids'   => [],
                'bonus_type'   => null,
                'bonus_ids'    => [],
                'post_type'    => null,
                'post_ids'     => [],
                'repeater_data' => [
                    ['title' => 'Elegi tu linea',  'subtitle' => 'Contacta una linea de atencion y pedi tu usuario sin formularios eternos.',     'icon' => 'fa-solid fa-layer-group'],
                    ['title' => 'Carga saldo',      'subtitle' => 'Realiza tu primera carga con los metodos disponibles de manera facil y rapida.', 'icon' => 'fa-solid fa-wallet'],
                    ['title' => 'Juga',             'subtitle' => 'Ingresa al casino con tu usuario, aprovecha los bonos y empeza a jugar.',        'icon' => 'fa-solid fa-gamepad'],
                ],
            ],
            [
                'section_key'  => 'lineas',
                'order'        => 1,
                'enabled'      => true,
                'kicker'       => 'Empeza a jugar',
                'title'        => 'Lineas de',
                'highlight'    => 'atencion',
                'subtitle'     => 'Habla con una linea, pedi tu usuario, carga saldo y entra al casino en minutos.',
                'content'      => '',
                'image'        => null,
                'action_text'  => '',
                'action_url'   => '',
                'line_ids'     => $lineIds,
                'raffle_type'  => null,
                'raffle_ids'   => [],
                'bonus_type'   => null,
                'bonus_ids'    => [],
                'post_type'    => null,
                'post_ids'     => [],
                'repeater_data' => [],
            ],
            [
                'section_key'  => 'sorteo',
                'order'        => 2,
                'enabled'      => true,
                'kicker'       => 'Muy pronto',
                'title'        => 'PROXIMOS',
                'highlight'    => 'SORTEOS',
                'subtitle'     => 'Nuevas oportunidades para ganar. Registrate y enterate antes que nadie.',
                'content'      => '',
                'image'        => null,
                'action_text'  => '',
                'action_url'   => '',
                'line_ids'     => [],
                'raffle_type'  => 'active',
                'raffle_ids'   => $raffleIds,
                'bonus_type'   => null,
                'bonus_ids'    => [],
                'post_type'    => null,
                'post_ids'     => [],
                'repeater_data' => [],
            ],
            [
                'section_key'  => 'nosotros',
                'order'        => 3,
                'enabled'      => true,
                'kicker'       => 'Sobre nosotros',
                'title'        => 'Casino online con atencion',
                'highlight'    => 'real',
                'subtitle'     => 'Una experiencia pensada para jugar facil: acceso rapido, bonos claros, sorteos activos y soporte humano para acompanarte.',
                'content'      => '',
                'image'        => null,
                'action_text'  => '',
                'action_url'   => '',
                'line_ids'     => [],
                'raffle_type'  => null,
                'raffle_ids'   => [],
                'bonus_type'   => null,
                'bonus_ids'    => [],
                'post_type'    => null,
                'post_ids'     => [],
                'repeater_data' => [
                    ['title' => 'Alta rapida',     'subtitle' => 'Contactas una linea y pedis tu usuario sin formularios eternos.',    'icon' => 'fa-solid fa-bolt'],
                    ['title' => 'Bonos vigentes',  'subtitle' => 'Bonos para recargar, arrancar con ventaja y jugar mas.',             'icon' => 'fa-solid fa-gift'],
                    ['title' => 'Sorteos activos', 'subtitle' => 'Premios y chances extra para usuarios que participan.',              'icon' => 'fa-solid fa-dice'],
                    ['title' => 'Soporte humano',  'subtitle' => 'Atencion directa para cargas, retiros, dudas y novedades.',         'icon' => 'fa-solid fa-headset'],
                ],
            ],
            [
                'section_key'  => 'bonos',
                'order'        => 4,
                'enabled'      => true,
                'kicker'       => 'Bonos para jugar mas',
                'title'        => 'Bonos',
                'highlight'    => 'activos',
                'subtitle'     => 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jugada.',
                'content'      => '',
                'image'        => null,
                'action_text'  => 'Ver todos',
                'action_url'   => '/bonos',
                'line_ids'     => [],
                'raffle_type'  => null,
                'raffle_ids'   => [],
                'bonus_type'   => 'active',
                'bonus_ids'    => $bonusIds,
                'post_type'    => null,
                'post_ids'     => [],
                'repeater_data' => [],
            ],
            [
                'section_key'  => 'blog',
                'order'        => 5,
                'enabled'      => true,
                'kicker'       => 'Noticias y jugadas',
                'title'        => 'Noticias y',
                'highlight'    => 'novedades',
                'subtitle'     => 'Enterate de novedades, sorteos, recomendaciones y bonos nuevos antes de que pasen.',
                'content'      => '',
                'image'        => null,
                'action_text'  => 'Ver novedades',
                'action_url'   => '/blog',
                'line_ids'     => [],
                'raffle_type'  => null,
                'raffle_ids'   => [],
                'bonus_type'   => null,
                'bonus_ids'    => [],
                'post_type'    => null,
                'post_ids'     => $postIds,
                'repeater_data' => [],
            ],
        ];

        foreach ($sections as $data) {
            HomeSection::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => null, 'section_key' => $data['section_key']],
                array_merge($data, ['vendor_id' => null])
            );
        }
    }

    private function seedPublicPages(): void
    {
        $directVendorIds = Vendor::where('is_direct', true)->pluck('id');

        $directLineIds = Line::withoutGlobalScopes()
            ->where('status', 'active')
            ->whereIn('vendor_id', $directVendorIds)
            ->orderBy('name')
            ->pluck('id')
            ->toArray();

        $cajeroLineIds = Line::withoutGlobalScopes()
            ->where('status', 'active')
            ->whereNotIn('vendor_id', $directVendorIds)
            ->orderBy('name')
            ->pluck('id')
            ->toArray();

        $directBonusIds = Bonus::withoutGlobalScopes()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->whereIn('vendor_id', $directVendorIds)
            ->latest('start_date')
            ->pluck('id')
            ->toArray();

        $cajeroBonusIds = Bonus::withoutGlobalScopes()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->whereNotIn('vendor_id', $directVendorIds)
            ->latest('start_date')
            ->pluck('id')
            ->toArray();

        $oficialRaffleIds = Raffle::withoutGlobalScopes()
            ->whereIn('vendor_id', $directVendorIds)
            ->whereIn('status', ['active', 'inactive'])
            ->orderBy('start_date')
            ->pluck('id')
            ->toArray();

        $cajeroRaffleIds = Raffle::withoutGlobalScopes()
            ->whereNotIn('vendor_id', $directVendorIds)
            ->whereIn('status', ['active', 'inactive'])
            ->orderBy('start_date')
            ->pluck('id')
            ->toArray();

        $vendorIds = Vendor::where('is_active', true)
            ->orderByDesc('is_direct')
            ->orderBy('name')
            ->pluck('id')
            ->toArray();

        $postIds = Post::withoutGlobalScopes()
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->take(12)
            ->pluck('id')
            ->toArray();

        $pages = [
            [
                'section_key'   => 'page.lineas',
                'order'         => 0,
                'enabled'       => true,
                'kicker'        => 'Lineas disponibles',
                'title'         => 'Elegi una linea',
                'highlight'     => 'activa',
                'subtitle'      => 'Pedi tu usuario, consulta plataformas disponibles y contacta al canal asignado para empezar a jugar.',
                'content'       => '',
                'image'         => null,
                'action_text'   => '',
                'action_url'    => '',
                'line_ids'      => [],
                'raffle_type'   => null,
                'raffle_ids'    => [],
                'bonus_type'    => null,
                'bonus_ids'     => [],
                'post_type'     => null,
                'post_ids'      => [],
                'repeater_data' => [
                    ['key' => 'red-picantes', 'title' => 'Red Picantes', 'subtitle' => 'Lineas oficiales de la red, seleccionadas para empezar rapido.', 'enabled' => true, 'icon' => 'fa-solid fa-crown', 'item_ids' => array_map('strval', $directLineIds)],
                    ['key' => 'cajeros', 'title' => 'Cajeros', 'subtitle' => 'Lineas activas publicadas por cajeros verificados.',              'enabled' => true, 'icon' => 'fa-solid fa-cash-register', 'item_ids' => array_map('strval', $cajeroLineIds)],
                ],
            ],
            [
                'section_key'   => 'page.bonos',
                'order'         => 0,
                'enabled'       => true,
                'kicker'        => 'Bonos disponibles',
                'title'         => 'Bonos',
                'highlight'     => 'activos',
                'subtitle'      => 'Promociones seleccionadas para arrancar mejor, recargar con ventaja y aprovechar cada jugada.',
                'content'       => '',
                'action_text'   => 'Lineas de atencion',
                'action_url'    => '/lineas',
                'line_ids'      => [],
                'raffle_type'   => null,
                'raffle_ids'    => [],
                'bonus_type'    => null,
                'bonus_ids'     => [],
                'post_type'     => null,
                'post_ids'      => [],
                'repeater_data' => [
                    ['key' => 'red-picantes', 'title' => 'Red Picantes', 'subtitle' => 'Bonos activos publicados por la red oficial.', 'enabled' => true, 'icon' => 'fa-solid fa-crown', 'item_ids' => array_map('strval', $directBonusIds)],
                    ['key' => 'cajeros', 'title' => 'Cajeros', 'subtitle' => 'Bonos activos publicados por cajeros externos.', 'enabled' => true, 'icon' => 'fa-solid fa-cash-register', 'item_ids' => array_map('strval', $cajeroBonusIds)],
                ],
            ],
            [
                'section_key'   => 'page.sorteos',
                'order'         => 0,
                'enabled'       => true,
                'kicker'        => 'Sorteos disponibles',
                'title'         => 'Sorteos',
                'highlight'     => 'activos',
                'subtitle'      => 'Participa en sorteos seleccionados de las lineas activas y acumula chances de ganar premios.',
                'content'       => '',
                'action_text'   => 'Ver lineas',
                'action_url'    => '/lineas',
                'line_ids'      => [],
                'raffle_type'   => null,
                'raffle_ids'    => [],
                'bonus_type'    => null,
                'bonus_ids'     => [],
                'post_type'     => null,
                'post_ids'      => [],
                'repeater_data' => [
                    ['key' => 'red-picantes', 'title' => 'Red Picantes', 'subtitle' => 'Sorteos organizados por la red oficial.', 'enabled' => true, 'icon' => 'fa-solid fa-crown', 'item_ids' => array_map('strval', $oficialRaffleIds)],
                    ['key' => 'cajeros', 'title' => 'Cajeros', 'subtitle' => 'Sorteos publicados por cajeros verificados.',    'enabled' => true, 'icon' => 'fa-solid fa-cash-register', 'item_ids' => array_map('strval', $cajeroRaffleIds)],
                ],
            ],
            [
                'section_key'   => 'page.cajeros',
                'order'         => 0,
                'enabled'       => true,
                'kicker'        => 'Cajeros verificados',
                'title'         => 'Elegi tu',
                'highlight'     => 'cajero',
                'subtitle'      => 'Todos los cajeros publicados tienen perfil propio, canales de contacto, lineas disponibles, bonos y sorteos asociados.',
                'content'       => 'Perfiles publicos listos para compartir',
                'image'         => null,
                'action_text'   => '',
                'action_url'    => '',
                'line_ids'      => [],
                'raffle_type'   => null,
                'raffle_ids'    => [],
                'bonus_type'    => null,
                'bonus_ids'     => [],
                'post_type'     => null,
                'post_ids'      => [],
                'repeater_data' => [
                    ['key' => 'general', 'title' => 'General', 'subtitle' => '', 'enabled' => true, 'icon' => 'fa-solid fa-cash-register', 'item_ids' => array_map('strval', $vendorIds)],
                ],
            ],
            [
                'section_key'   => 'page.novedades',
                'order'         => 0,
                'enabled'       => true,
                'kicker'        => 'Noticias y jugadas',
                'title'         => 'Novedades de',
                'highlight'     => 'RED PICANTES',
                'subtitle'      => 'Bonos, sorteos activos, recomendaciones para jugar mejor y avisos importantes del casino.',
                'content'       => '',
                'action_text'   => '',
                'action_url'    => '',
                'line_ids'      => [],
                'raffle_type'   => null,
                'raffle_ids'    => [],
                'bonus_type'    => null,
                'bonus_ids'     => [],
                'post_type'     => null,
                'post_ids'      => [],
                'repeater_data' => [
                    ['key' => 'general', 'title' => 'General', 'subtitle' => '', 'enabled' => true, 'icon' => 'fa-solid fa-newspaper', 'item_ids' => array_map('strval', $postIds)],
                ],
            ],
        ];

        foreach ($pages as $data) {
            HomeSection::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => null, 'section_key' => $data['section_key']],
                array_merge($data, ['vendor_id' => null])
            );
        }
    }
}
