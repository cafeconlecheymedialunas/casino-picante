<?php

namespace Tests\Feature;

use App\Livewire\Settings;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vendor;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BrandingSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_branding_settings_have_defaults_when_missing(): void
    {
        $this->assertSame(Setting::DEFAULT_SITE_TITLE, Setting::siteTitle());
        $this->assertSame('', Setting::siteLogoUrl());
        $this->assertStringEndsWith('/favicon.ico', Setting::siteFaviconUrl());

        $this->assertDatabaseHas('settings', [
            'key' => 'site_title',
            'value' => Setting::DEFAULT_SITE_TITLE,
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'site_logo',
            'value' => Setting::DEFAULT_LOGO,
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'site_favicon',
            'value' => Setting::DEFAULT_FAVICON,
        ]);
    }

    public function test_cajero_can_set_page_title_logo_and_favicon(): void
    {
        Storage::fake('public');
        [$user, $vendor] = $this->cajeroVendor();

        $this->actingAs($user)->withSession(['active_vendor_id' => $vendor->id]);

        Livewire::test(Settings::class)
            ->set('siteTitle', 'Casino Marca Test')
            ->set('siteLogoUpload', UploadedFile::fake()->image('logo.png', 360, 120))
            ->set('siteFaviconUpload', UploadedFile::fake()->image('favicon.png', 64, 64))
            ->call('saveBrandingAssets')
            ->assertHasNoErrors();

        $logo = Setting::query()->where('key', 'site_logo')->value('value');
        $favicon = Setting::query()->where('key', 'site_favicon')->value('value');

        $this->assertSame('Casino Marca Test', Setting::siteTitle());
        $this->assertStringStartsWith('branding/', $logo);
        $this->assertStringStartsWith('branding/', $favicon);
        Storage::disk('public')->assertExists($logo);
        Storage::disk('public')->assertExists($favicon);

        $this->get(route('frontend.home'))
            ->assertOk()
            ->assertSee('<title>Casino Marca Test</title>', false)
            ->assertSee('href="'.Setting::assetUrl($favicon, Setting::DEFAULT_FAVICON).'"', false)
            ->assertSee('src="'.Setting::assetUrl($logo).'"', false)
            ->assertSee('alt="Casino Marca Test"', false);
    }

    private function cajeroVendor(): array
    {
        $role = Role::firstOrCreate(['name' => Roles::CAJERO], ['label' => 'Cajero']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'username' => 'cajero_'.uniqid(),
            'status' => 'active',
        ]);

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => 'Vendor '.uniqid(),
            'slug' => 'vendor-'.uniqid(),
            'is_active' => true,
        ]);

        $user->forceFill(['vendor_id' => $vendor->id])->save();

        return [$user->fresh('role'), $vendor];
    }
}
