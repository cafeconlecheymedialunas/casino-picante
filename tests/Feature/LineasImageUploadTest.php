<?php

namespace Tests\Feature;

use App\Livewire\Lineas;
use App\Models\Line;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
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
            'vendor_id' => $vendorId = Vendor::create([
                'user_id' => $user->id,
                'name' => 'Vendor Imagen',
                'slug' => 'vendor-imagen',
                'is_active' => true,
            ])->id,
            'name' => 'Linea Imagen',
            'status' => 'active',
        ]);

        session(['active_vendor_id' => $vendorId]);

        Livewire::actingAs($user)
            ->test(Lineas::class)
            ->call('openEditModal', $line->id)
            ->set('portadaUpload', UploadedFile::fake()->image('portada.jpg', 851, 315))
            ->set('perfilUpload', UploadedFile::fake()->image('perfil.jpg', 800, 800))
            ->call('saveLine')
            ->assertHasNoErrors();

        $line->refresh();

        $this->assertStringStartsWith("/storage/vendors/{$vendorId}/lineas/portadas/", $line->portada_url);
        $this->assertStringStartsWith("/storage/vendors/{$vendorId}/lineas/perfiles/", $line->perfil_url);
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
            'vendor_id' => $vendorId = Vendor::create([
                'user_id' => $user->id,
                'name' => 'Vendor Portada',
                'slug' => 'vendor-portada',
                'is_active' => true,
            ])->id,
            'name' => 'Linea Portada Grande',
            'status' => 'active',
        ]);

        session(['active_vendor_id' => $vendorId]);

        Livewire::actingAs($user)
            ->test(Lineas::class)
            ->call('openEditModal', $line->id)
            ->set('portadaUpload', UploadedFile::fake()->image('portada-grande.jpg', 851, 315)->size(10 * 1024))
            ->call('saveLine')
            ->assertHasNoErrors();

        $line->refresh();

        $this->assertStringStartsWith("/storage/vendors/{$vendorId}/lineas/portadas/", $line->portada_url);
    }
}
