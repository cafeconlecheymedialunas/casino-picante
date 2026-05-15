<?php

namespace Database\Seeders;

use App\Models\Raffle;
use App\Models\Line;
use App\Models\Platform;
use Illuminate\Support\Facades\DB;

// Clear existing raffles to have a clean state for the demo
DB::table('line_raffle')->truncate();
Raffle::truncate();

$lines = Line::all();
$platform = Platform::first();

if ($lines->isEmpty()) {
    echo "No lines found. Please seed lines first.\n";
    exit;
}

// 1. ACTIVE RAFFLE (The one usually shown in Home)
$activeRaffle = Raffle::create([
    'title' => 'GRAN SORTEO SEMANAL',
    'description' => 'Suma puntos a medida que generes netwin y participa por increíbles premios.',
    'status' => 'active',
    'start_date' => now()->subDays(2),
    'end_date' => now()->addDays(5),
    'start_number' => 1000,
    'end_number' => 5000,
    'prizes' => [
        ['position' => 1, 'name' => 'Viaje por $7.000', 'amount' => 500, 'image' => null],
        ['position' => 2, 'name' => 'Bajaj Rouser ns200 0km', 'amount' => 250, 'image' => null],
        ['position' => 3, 'name' => 'MacBook Pro 16 M5', 'amount' => 150, 'image' => null],
    ]
]);
$activeRaffle->lines()->attach($lines->pluck('id'));

// 2. UPCOMING RAFFLE
$upcomingRaffle = Raffle::create([
    'title' => 'SORTEO DE LANZAMIENTO JUNIO',
    'description' => 'Preparate para el sorteo mas grande del año. Proximamente mas informacion.',
    'status' => 'active',
    'start_date' => now()->addDays(10),
    'end_date' => now()->addDays(20),
    'start_number' => 5001,
    'prizes' => [
        ['position' => 1, 'name' => 'iPhone 15 Pro Max', 'amount' => 1200, 'image' => null],
        ['position' => 2, 'name' => 'PlayStation 5 Slim', 'amount' => 600, 'image' => null],
    ]
]);
$upcomingRaffle->lines()->attach($lines->take(2)->pluck('id'));

// 3. FINISHED RAFFLE
$finishedRaffle = Raffle::create([
    'title' => 'SORTEO FLASH RELAMPAGO',
    'description' => 'Este sorteo ya ha finalizado. Gracias a todos por participar.',
    'status' => 'finished',
    'start_date' => now()->subDays(10),
    'end_date' => now()->subDays(3),
    'winner_number' => 1234,
    'prizes' => [
        ['position' => 1, 'name' => 'Créditos por $50.000', 'amount' => 50000, 'image' => null],
    ]
]);
$finishedRaffle->lines()->attach($lines->random(1)->pluck('id'));

echo "Demo raffles created successfully!\n";
