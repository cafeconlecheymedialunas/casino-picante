<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Line;
use App\Models\Platform;
use App\Models\DashboardNotification;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $line = Line::firstOrCreate(
            ['name' => 'Línea de Prueba'],
            ['status' => 'active', 'type' => 'test', 'phone' => '123456789']
        );

        $platform = Platform::firstOrCreate(
            ['slug' => 'casino-central'],
            ['name' => 'Casino Central', 'is_active' => true]
        );

        if (! $line->platforms()->where('platform_id', $platform->id)->exists()) {
            $line->platforms()->attach($platform->id);
        }

        for ($i = 1; $i <= 10; $i++) {
            User::firstOrCreate(
                ['email' => "cliente$i@ejemplo.com"],
                [
                    'name' => "Cliente Prueba $i",
                    'password' => bcrypt('password'),
                    'status' => 'active'
                ]
            );
        }

        if (DashboardNotification::count() === 0) {
            DashboardNotification::insert([
                [
                    'title' => 'Bienvenido al panel',
                    'message' => 'El sistema de gestión está configurado y listo para operar. Revisa las métricas del dashboard.',
                    'type' => 'info',
                    'link' => '/dashboard',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Nuevo bono disponible',
                    'message' => 'Se ha creado un bono del 100% para nuevos depósitos. Actívalo desde la sección de Bonos.',
                    'type' => 'success',
                    'link' => '/bonos',
                    'created_at' => now()->subHours(2),
                    'updated_at' => now()->subHours(2),
                ],
                [
                    'title' => 'Sorteo próximo a vencer',
                    'message' => 'El sorteo semanal cierra en 24 horas. Asegura los números pendientes.',
                    'type' => 'warning',
                    'link' => '/sorteos',
                    'created_at' => now()->subHours(5),
                    'updated_at' => now()->subHours(5),
                ],
                [
                    'title' => 'Ticket sin respuesta',
                    'message' => 'Hay 3 tickets abiertos hace más de 2 horas sin respuesta del equipo.',
                    'type' => 'danger',
                    'link' => '/tickets',
                    'created_at' => now()->subHours(1),
                    'updated_at' => now()->subHours(1),
                ],
            ]);
        }
    }
}
