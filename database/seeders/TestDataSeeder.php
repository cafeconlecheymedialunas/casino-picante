<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Line;
use App\Models\Platform;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $line = Line::create([
            'name' => 'Línea de Prueba', 
            'status' => 'active',
            'type' => 'test',
            'phone' => '123456789'
        ]);
        
        $platform = Platform::create([
            'name' => 'Casino Central', 
            'slug' => 'casino-central',
            'is_active' => true
        ]);
        
        $line->platforms()->attach($platform->id);

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Cliente Prueba $i",
                'email' => "cliente$i@ejemplo.com",
                'password' => bcrypt('password'),
                'status' => 'active'
            ]);
        }
    }
}
