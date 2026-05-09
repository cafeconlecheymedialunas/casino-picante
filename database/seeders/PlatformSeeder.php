<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            [
                'name' => 'Web',
                'slug' => 'web',
                'is_active' => true,
                'website_url' => 'https://example.com',
                'description' => 'Plataforma web principal',
            ],
            [
                'name' => 'iOS',
                'slug' => 'ios',
                'is_active' => true,
                'website_url' => 'https://apps.apple.com',
                'description' => 'Aplicación para dispositivos iOS',
            ],
            [
                'name' => 'Android',
                'slug' => 'android',
                'is_active' => true,
                'website_url' => 'https://play.google.com',
                'description' => 'Aplicación para dispositivos Android',
            ],
            [
                'name' => 'Desktop',
                'slug' => 'desktop',
                'is_active' => true,
                'website_url' => 'https://desktop.example.com',
                'description' => 'Aplicación de escritorio',
            ],
            [
                'name' => 'API',
                'slug' => 'api',
                'is_active' => true,
                'website_url' => 'https://api.example.com',
                'description' => 'Plataforma de API para integraciones',
            ],
        ];

        foreach ($platforms as $platform) {
            Platform::updateOrCreate(
                ['slug' => $platform['slug']],
                $platform
            );
        }

        $this->command->info('Plataformas sembradas exitosamente.');
    }
}
