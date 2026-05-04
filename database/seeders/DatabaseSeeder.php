<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Line;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create clients (users) - estos son los que apuestan
        $clients = [
            ['name' => 'Juan Perez', 'email' => 'juan@test.com', 'phone' => '+54 9 11 1111 1111'],
            ['name' => 'Maria Lopez', 'email' => 'maria@test.com', 'phone' => '+54 9 11 2222 2222'],
            ['name' => 'Carlos Garcia', 'email' => 'carlos@test.com', 'phone' => '+54 9 11 3333 3333'],
            ['name' => 'Ana Rodriguez', 'email' => 'ana@test.com', 'phone' => '+54 9 11 4444 4444'],
        ];

        $clientIds = [];
        foreach ($clients as $clientData) {
            $client = User::create(array_merge($clientData, [
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]));
            $clientIds[] = $client->id;
        }

        // Create lines
        $line1 = Line::create([
            'name' => 'Línea 1',
            'icon' => '🔥',
            'description' => 'Atención principal 24/7',
            'whatsapp' => '+54 9 11 1234-1111',
            'whatsapp_message' => 'Hola! Gracias por contactarnos. ¿En qué podemos ayudarte?',
            'telegram' => '@redpicantes_l1',
            'status' => 'active',
        ]);

        $line2 = Line::create([
            'name' => 'Línea 2',
            'icon' => '💰',
            'description' => 'Pagos y retiros',
            'whatsapp' => '+54 9 11 1234-2222',
            'whatsapp_message' => 'Hola! Equipo de pagos aquí. ¿En qué podemos ayudarte?',
            'telegram' => '@redpicantes_l2',
            'status' => 'active',
        ]);

        $line3 = Line::create([
            'name' => 'Línea 3',
            'icon' => '👑',
            'description' => 'VIP - Alto rolling',
            'whatsapp' => '+54 9 11 1234-3333',
            'instagram' => '@redpicantes.l3',
            'status' => 'active',
        ]);

        // Create agents (personal que atiende)
        $agent1 = Agent::create([
            'name' => 'Martin Rios',
            'email' => 'martin@redpicantes.com',
            'password' => Hash::make('password123'),
            'phone' => '+54 9 11 5555 5555',
            'status' => 'active',
        ]);

        $agent2 = Agent::create([
            'name' => 'Lucia Fernandez',
            'email' => 'lucia@redpicantes.com',
            'password' => Hash::make('password123'),
            'phone' => '+54 9 11 6666 6666',
            'parent_id' => $agent1->id,
            'status' => 'active',
        ]);

        $agent3 = Agent::create([
            'name' => 'Diego Martinez',
            'email' => 'diego@redpicantes.com',
            'password' => Hash::make('password123'),
            'phone' => '+54 9 11 7777 7777',
            'parent_id' => $agent1->id,
            'status' => 'active',
        ]);

        // Update lines with encargado_id
        $line1->update(['encargado_id' => $agent1->id]);
        $line2->update(['encargado_id' => $agent2->id]);
        $line3->update(['encargado_id' => $agent3->id]);

        // Create line_agents (conexión agente-línea con rol)
        DB::table('line_agents')->insert([
            'line_id' => $line1->id,
            'agent_id' => $agent1->id,
            'role' => 'encargado',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('line_agents')->insert([
            'line_id' => $line1->id,
            'agent_id' => $agent2->id,
            'role' => 'miembro',
            'parent_id' => $agent1->id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('line_agents')->insert([
            'line_id' => $line2->id,
            'agent_id' => $agent2->id,
            'role' => 'encargado',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('line_agents')->insert([
            'line_id' => $line3->id,
            'agent_id' => $agent3->id,
            'role' => 'encargado',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create clients in line_clients (opcional - para demo)
        DB::table('line_clients')->insert([
            'line_id' => $line1->id,
            'user_id' => $clientIds[0],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('line_clients')->insert([
            'line_id' => $line1->id,
            'user_id' => $clientIds[1],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create promotions
        $promos = [
            ['title' => 'Bono Bienvenida 200%', 'description' => 'Hasta $50.000 + 100 giros gratis', 'code' => 'PICANTE200', 'icon' => '🔥', 'start_date' => now(), 'end_date' => now()->addDays(7), 'status' => 'published'],
            ['title' => 'Cashback 15% Lunes', 'description' => 'Devolución del 15% los lunes', 'code' => 'CASH15', 'icon' => '💰', 'start_date' => now(), 'end_date' => null, 'status' => 'published'],
            ['title' => 'Reload Weekend 50%', 'description' => 'Bono de recarga 50%', 'code' => 'WEEKEND50', 'icon' => '♻️', 'start_date' => now()->addDays(3), 'end_date' => now()->addDays(5), 'status' => 'draft'],
        ];

        foreach ($promos as $promo) {
            Promotion::create($promo);
        }

        // Create posts (novedades)
        $posts = [
            ['title' => 'Llegó la Mega Slot Inferno', 'slug' => 'mega-slot-inferno', 'excerpt' => 'El nuevo slot exclusivo con multiplicadores hasta x500', 'content' => 'Mega Slot Inferno trae el calor...', 'type' => 'novedad', 'status' => 'published'],
            ['title' => 'Nuevos métodos de pago', 'slug' => 'nuevos-metodos-pago', 'excerpt' => 'Ahora podés depositar con MercadoPago', 'content' => 'Nuevos métodos de pago...', 'type' => 'novedad', 'status' => 'published'],
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }

        // Create tickets
        $tickets = [
            ['user_id' => $clientIds[0], 'line_id' => $line1->id, 'subject' => 'No puedo retirar', 'status' => 'open', 'priority' => 'high'],
            ['user_id' => $clientIds[1], 'line_id' => $line1->id, 'subject' => 'Bono no acreditado', 'status' => 'progress', 'priority' => 'medium'],
        ];

        foreach ($tickets as $ticket) {
            Ticket::create($ticket);
        }

        echo "Database seeded!\n";
    }
}
