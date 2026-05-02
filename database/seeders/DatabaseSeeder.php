<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Line;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Sofia Admin',
            'email' => 'admin@redpicantes.com',
            'password' => Hash::make('password123'),
            'phone' => '+54 9 11 1234 5678',
            'status' => 'active',
        ]);

        // Create sample users
        $users = [
            ['name' => 'Juan Perez', 'email' => 'juan@test.com', 'phone' => '+54 9 11 1111 1111'],
            ['name' => 'Maria Lopez', 'email' => 'maria@test.com', 'phone' => '+54 9 11 2222 2222'],
            ['name' => 'Carlos Garcia', 'email' => 'carlos@test.com', 'phone' => '+54 9 11 3333 3333'],
            ['name' => 'Ana Rodriguez', 'email' => 'ana@test.com', 'phone' => '+54 9 11 4444 4444'],
        ];

        foreach ($users as $userData) {
            User::create(array_merge($userData, [
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]));
        }

        // Create lines
        $lines = [
            ['name' => 'Línea 1', 'icon' => '🔥', 'description' => 'Atención principal 24/7', 'whatsapp' => '+54 9 11 1234-1111', 'whatsapp_message' => 'Hola! Gracias por contactarnos. ¿En qué podemos ayudarte?', 'telegram' => '@redpicantes_l1', 'status' => 'active'],
            ['name' => 'Línea 2', 'icon' => '💰', 'description' => 'Pagos y retiros', 'whatsapp' => '+54 9 11 1234-2222', 'whatsapp_message' => 'Hola! Equipo de pagos aquí. ¿En qué podemos ayudarte?', 'telegram' => '@redpicantes_l2', 'status' => 'active'],
            ['name' => 'Línea 3', 'icon' => '👑', 'description' => 'VIP - Alto rolling', 'whatsapp' => '+54 9 11 1234-3333', 'instagram' => '@redpicantes.l3', 'status' => 'active'],
        ];

        foreach ($lines as $line) {
            Line::create($line);
        }

        // Create promotions
        $promos = [
            ['title' => 'Bono Bienvenida 200%', 'description' => 'Hasta $50.000 + 100 giros gratis', 'code' => 'PICANTE200', 'icon' => '🔥', 'start_date' => now(), 'end_date' => now()->addDays(7), 'status' => 'published', 'lines' => ['L1', 'L2', 'L3']],
            ['title' => 'Cashback 15% Lunes', 'description' => 'Devolución del 15% los lunes', 'code' => 'CASH15', 'icon' => '💰', 'start_date' => now(), 'end_date' => null, 'status' => 'published', 'lines' => ['L1', 'L2']],
            ['title' => 'Reload Weekend 50%', 'description' => 'Bono de recarga 50%', 'code' => 'WEEKEND50', 'icon' => '♻️', 'start_date' => now()->addDays(3), 'end_date' => now()->addDays(5), 'status' => 'draft', 'lines' => ['L1', 'L2', 'L3']],
        ];

        foreach ($promos as $promo) {
            Promotion::create($promo);
        }

        // Create posts (novedades)
        $posts = [
            ['title' => 'Llegó la Mega Slot Inferno', 'slug' => 'mega-slot-inferno', 'excerpt' => 'El nuevo slot exclusivo con multiplicadores hasta x500', 'content' => 'Mega Slot Inferno trae el calor de RED PICANTES a tu pantalla...', 'type' => 'novedad', 'status' => 'published', 'published_at' => now()],
            ['title' => 'Nuevos métodos de pago', 'slug' => 'nuevos-metodos-pago', 'excerpt' => 'Ahora podés depositar con MercadoPago y más', 'content' => 'Agregamos nuevos métodos de pago para tu comodidad...', 'type' => 'novedad', 'status' => 'published', 'published_at' => now()->subDays(3)],
            ['title' => 'Resultados del Torneo', 'slug' => 'resultados-torneo', 'excerpt' => 'Conocé a los ganadores del mes', 'content' => 'Felicitamos a todos los participantes...', 'type' => 'blog', 'status' => 'published', 'published_at' => now()->subDays(7)],
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }

        // Create tickets
        $tickets = [
            ['user_id' => 1, 'line_id' => 'L1', 'subject' => 'No puedo retirar', 'status' => 'open', 'priority' => 'high'],
            ['user_id' => 2, 'line_id' => 'L2', 'subject' => 'Bono no acreditado', 'status' => 'progress', 'priority' => 'medium'],
            ['user_id' => 3, 'line_id' => 'L1', 'subject' => 'Consulta sobre promos', 'status' => 'closed', 'priority' => 'low'],
        ];

        foreach ($tickets as $ticket) {
            Ticket::create($ticket);
        }

        // Create agents (parent)
        $parentAgent = Agent::create([
            'name' => 'Martin Rios',
            'email' => 'martin@redpicantes.com',
            'password' => Hash::make('password123'),
            'phone' => '+54 9 11 5555 5555',
            'role' => 'parent',
            'status' => 'active',
            'lines' => ['L1', 'L2'],
        ]);

        // Create child agents
        Agent::create([
            'name' => 'Lucia Fernandez',
            'email' => 'lucia@redpicantes.com',
            'password' => Hash::make('password123'),
            'phone' => '+54 9 11 6666 6666',
            'parent_id' => $parentAgent->id,
            'role' => 'child',
            'status' => 'active',
            'lines' => ['L1'],
        ]);

        Agent::create([
            'name' => 'Diego Martinez',
            'email' => 'diego@redpicantes.com',
            'password' => Hash::make('password123'),
            'phone' => '+54 9 11 7777 7777',
            'parent_id' => $parentAgent->id,
            'role' => 'child',
            'status' => 'active',
            'lines' => ['L1'],
        ]);

        echo "Database seeded!\n";
    }
}
