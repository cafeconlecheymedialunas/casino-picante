<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        $agents = [
            [
                'username'  => 'pablo_s',
                'name'      => 'Pablo',
                'apellido'  => 'Suarez',
                'email'     => 'pablo@agencia.com',
                'phone'     => '+54 9 11 8001 0001',
                'cargo'     => 'super_agente',
                'status'    => 'active',
            ],
            [
                'username'  => 'valentina_t',
                'name'      => 'Valentina',
                'apellido'  => 'Torres',
                'email'     => 'valentina@agencia.com',
                'phone'     => '+54 9 11 8001 0002',
                'cargo'     => 'agente',
                'status'    => 'active',
            ],
            [
                'username'  => 'facundo_g',
                'name'      => 'Facundo',
                'apellido'  => 'Gomez',
                'email'     => 'facundo@agencia.com',
                'phone'     => '+54 9 11 8001 0003',
                'cargo'     => 'agente',
                'status'    => 'active',
            ],
            [
                'username'  => 'camila_h',
                'name'      => 'Camila',
                'apellido'  => 'Herrera',
                'email'     => 'camila@agencia.com',
                'phone'     => '+54 9 11 8001 0004',
                'cargo'     => 'agente',
                'status'    => 'active',
            ],
            [
                'username'  => 'nicolas_b',
                'name'      => 'Nicolas',
                'apellido'  => 'Blanco',
                'email'     => 'nicolas@agencia.com',
                'phone'     => '+54 9 11 8001 0005',
                'cargo'     => 'super_agente',
                'status'    => 'active',
            ],
        ];

        foreach ($agents as $data) {
            Agent::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password123')])
            );
        }

        $this->command->info('5 agentes creados.');
    }
}
