<?php

namespace Database\Seeders;

use App\Models\Line;
use App\Models\Platform;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\User;
use Illuminate\Database\Seeder;

class RaffleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $lines = Line::withoutGlobalScopes()->where('status', 'active')->get();
        $platform = Platform::withoutGlobalScopes()->first();

        if ($lines->isEmpty()) {
            $this->command->warn('No hay líneas activas. Ejecuta el seeder principal primero.');
            return;
        }

        $clients = User::withoutGlobalScopes()->where('status', 'active')->take(10)->get();

        // 1. Sorteo activo (se muestra en la home)
        $active = Raffle::withoutGlobalScopes()->updateOrCreate(
            ['title' => 'GRAN SORTEO SEMANAL'],
            [
                'vendor_id'      => null,
                'slug'           => 'gran-sorteo-semanal',
                'description'    => 'Suma puntos a medida que generes netwin y participá por increíbles premios.',
                'status'         => 'active',
                'start_date'     => now()->subDays(2),
                'end_date'       => now()->addDays(5),
                'start_number'   => 1000,
                'end_number'     => 5000,
                'numbers_limit'  => 4000,
                'winner_user_id' => null,
                'winner_number'  => null,
                'line_id'        => $lines->first()->id,
                'platform_id'    => $platform?->id,
                'prizes' => [
                    ['position' => 1, 'name' => 'Viaje por $7.000',      'amount' => 7000, 'image' => 'https://picsum.photos/id/1049/480/260'],
                    ['position' => 2, 'name' => 'Bajaj Rouser NS200 0km','amount' => 3200, 'image' => 'https://picsum.photos/id/1082/480/260'],
                    ['position' => 3, 'name' => 'MacBook Pro 16 M5',     'amount' => 2500, 'image' => 'https://picsum.photos/id/1/480/260'],
                ],
            ]
        );
        $active->lines()->sync($lines->take(3)->pluck('id')->toArray());

        foreach ($clients->take(15) as $i => $client) {
            RaffleNumber::withoutGlobalScopes()->firstOrCreate(
                ['raffle_id' => $active->id, 'user_id' => $client->id],
                [
                    'line_id' => $lines[$i % $lines->count()]->id,
                    'number' => 1000 + $i + 1,
                ]
            );
        }

        // 2. Sorteo próximo
        $upcoming = Raffle::withoutGlobalScopes()->updateOrCreate(
            ['title' => 'SORTEO DE LANZAMIENTO JUNIO'],
            [
                'vendor_id'      => null,
                'slug'           => 'sorteo-de-lanzamiento-junio',
                'description'    => 'Preparate para el sorteo más grande del año. Próximamente más información.',
                'status'         => 'active',
                'start_date'     => now()->addDays(1),
                'end_date'       => now()->addDays(20),
                'start_number'   => 5001,
                'end_number'     => 9999,
                'numbers_limit'  => 4999,
                'winner_user_id' => null,
                'winner_number'  => null,
                'line_id'        => $lines->first()->id,
                'platform_id'    => $platform?->id,
                'prizes' => [
                    ['position' => 1, 'name' => 'iPhone 15 Pro Max',  'amount' => 1800, 'image' => 'https://picsum.photos/id/1040/480/260'],
                    ['position' => 2, 'name' => 'PlayStation 5 Slim', 'amount' => 900,  'image' => 'https://picsum.photos/id/1041/480/260'],
                ],
            ]
        );
        $upcoming->lines()->sync($lines->take(2)->pluck('id')->toArray());

        // 3. Sorteo finalizado
        $finished = Raffle::withoutGlobalScopes()->updateOrCreate(
            ['title' => 'SORTEO FLASH RELÁMPAGO'],
            [
                'vendor_id'      => null,
                'slug'           => 'sorteo-flash-relampago',
                'description'    => 'Este sorteo ya finalizó. Gracias a todos por participar.',
                'status'         => 'finished',
                'start_date'     => now()->subDays(10),
                'end_date'       => now()->subDays(3),
                'start_number'   => 1,
                'end_number'     => 500,
                'numbers_limit'  => 500,
                'winner_number'  => 234,
                'winner_user_id' => $clients->first()?->id,
                'line_id'        => $lines->first()->id,
                'platform_id'    => $platform?->id,
                'prizes' => [
                    ['position' => 1, 'name' => 'Créditos por $50.000', 'amount' => 50000, 'image' => 'https://picsum.photos/id/1042/480/260'],
                ],
            ]
        );
        $finished->lines()->sync([$lines->random()->id]);

        $this->command->info('RaffleDemoSeeder: 3 sorteos creados/actualizados.');
    }
}
