<?php

namespace Database\Seeders;

use App\Models\Bonus;
use App\Models\HomeSection;
use App\Models\Line;
use App\Models\Post;
use App\Models\Raffle;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        $lineIds = Line::withoutGlobalScopes()
            ->where('status', 'active')
            ->orderByRaw('EXISTS(SELECT 1 FROM vendors WHERE vendors.id = lines.vendor_id AND vendors.is_direct = 1) DESC')
            ->orderBy('name')
            ->take(6)
            ->pluck('id')
            ->toArray();

        $raffleIds = Raffle::withoutGlobalScopes()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->orderBy('end_date')
            ->take(5)
            ->pluck('id')
            ->toArray();

        $bonusIds = Bonus::withoutGlobalScopes()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->orderByRaw('EXISTS(SELECT 1 FROM vendors WHERE vendors.id = bonuses.vendor_id AND vendors.is_direct = 1) DESC')
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

        $sections = [
            'como-empezar' => [
                'kicker' => 'Como funciona',
                'title' => 'Empeza en',
                'highlight' => '3 pasos',
                'subtitle' => 'Sin vueltas: contacto, carga y juego. Si necesitás ayuda, una persona te responde.',
                'enabled' => true,
                'repeater_data' => json_encode([
                    ['title' => 'Elegí tu línea', 'subtitle' => 'Contactá una línea de atención y pedí tu usuario sin formularios eternos.'],
                    ['title' => 'Cargá saldo', 'subtitle' => 'Realizá tu primera carga con los métodos disponibles de manera fácil y rápida.'],
                    ['title' => 'Jugá', 'subtitle' => 'Ingresá al casino con tu usuario, aprovechá los bonos y empezá a jugar.'],
                ]),
            ],
            'lineas' => [
                'kicker' => 'Empeza a jugar',
                'title' => 'Líneas de',
                'highlight' => 'atención',
                'subtitle' => 'Hablá con una línea, pedí tu usuario, cargá saldo y entrá al casino en minutos.',
                'enabled' => true,
                'line_ids' => json_encode($lineIds),
            ],
            'sorteo' => [
                'kicker' => 'Muy pronto',
                'title' => 'PRÓXIMOS',
                'highlight' => 'SORTEOS',
                'subtitle' => 'Nuevas oportunidades para ganar. Registrate y enterate antes que nadie.',
                'enabled' => true,
                'raffle_ids' => json_encode($raffleIds),
            ],
            'nosotros' => [
                'kicker' => 'Sobre nosotros',
                'title' => 'Casino online con atención',
                'highlight' => 'real',
                'subtitle' => 'Una experiencia pensada para jugar fácil: acceso rápido, bonos claros, sorteos activos y soporte humano para acompañarte.',
                'enabled' => true,
                'repeater_data' => json_encode([
                    ['title' => 'Alta rápida', 'subtitle' => 'Contactás una línea y pedís tu usuario sin formularios eternos.'],
                    ['title' => 'Bonos vigentes', 'subtitle' => 'Bonos para recargar, arrancar con ventaja y jugar más.'],
                    ['title' => 'Sorteos activos', 'subtitle' => 'Premios y chances extra para usuarios que participan.'],
                    ['title' => 'Soporte humano', 'subtitle' => 'Atención directa para cargas, retiros, dudas y novedades.'],
                ]),
            ],
            'bonos' => [
                'kicker' => 'Bonos para jugar más',
                'title' => 'Bonos',
                'highlight' => 'activos',
                'subtitle' => 'Bonos vigentes para arrancar mejor, recargar con ventaja y aprovechar cada jugada.',
                'action_text' => 'Ver todos',
                'action_url' => '/bonos',
                'enabled' => true,
                'bonus_ids' => json_encode($bonusIds),
            ],
            'blog' => [
                'kicker' => 'Noticias y jugadas',
                'title' => 'Noticias y',
                'highlight' => 'novedades',
                'subtitle' => 'Enterate de novedades, sorteos, recomendaciones y bonos nuevos antes de que pasen.',
                'action_text' => 'Ver novedades',
                'action_url' => '/blog',
                'enabled' => true,
                'post_ids' => json_encode($postIds),
            ],
        ];

        foreach ($sections as $key => $data) {
            HomeSection::withoutGlobalScopes()->updateOrCreate(
                ['vendor_id' => null, 'section_key' => $key],
                $data
            );
        }

        $this->command->info('HomeSections pobladas correctamente.');
        $this->command->info('  Líneas seleccionadas: ' . count($lineIds));
        $this->command->info('  Sorteos seleccionados: ' . count($raffleIds));
        $this->command->info('  Bonos seleccionados: ' . count($bonusIds));
        $this->command->info('  Posts seleccionados: ' . count($postIds));
    }
}
