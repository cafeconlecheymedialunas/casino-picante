<?php

namespace Tests\Feature;

use App\Livewire\Lineas;
use App\Models\Line;
use App\Models\Role;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LineasImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_line_images_can_be_uploaded_from_editor(): void
    {
        Storage::fake('public');

        $role = Role::firstOrCreate(['name' => Roles::ADMIN], ['label' => 'Admin']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $line = Line::create([
            'name' => 'Linea Imagen',
            'status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(Lineas::class)
            ->call('openEditModal', $line->id)
            ->set('portadaUpload', UploadedFile::fake()->image('portada.jpg', 851, 315))
            ->set('perfilUpload', UploadedFile::fake()->image('perfil.jpg', 800, 800))
            ->call('saveLine')
            ->assertHasNoErrors();

        $line->refresh();

        $this->assertStringStartsWith('/storage/lineas/portadas/', $line->portada_url);
        $this->assertStringStartsWith('/storage/lineas/perfiles/', $line->perfil_url);
        Storage::disk('public')->assertExists(substr($line->portada_url, 9));
        Storage::disk('public')->assertExists(substr($line->perfil_url, 9));
    }

    public function test_line_editor_accepts_larger_cover_images(): void
    {
        Storage::fake('public');

        $role = Role::firstOrCreate(['name' => Roles::ADMIN], ['label' => 'Admin']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $line = Line::create([
            'name' => 'Linea Portada Grande',
            'status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(Lineas::class)
            ->call('openEditModal', $line->id)
            ->set('portadaUpload', UploadedFile::fake()->image('portada-grande.jpg', 851, 315)->size(10 * 1024))
            ->call('saveLine')
            ->assertHasNoErrors();

        $line->refresh();

        $this->assertStringStartsWith('/storage/lineas/portadas/', $line->portada_url);
    }
}
