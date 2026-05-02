<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;

class Ajustes extends Component
{
    public $activeTab = 'general';

    public $settings = [];

    protected $defaults = [
        'site_name' => 'RED PICANTES',
        'site_url' => 'https://redpicantes.com',
        'timezone' => 'America/Argentina/Buenos_Aires',
        'open_registration' => true,
        'email_verification' => true,
        'kyc_required' => true,
        'maintenance_mode' => false,
        'min_deposit' => 1000,
        'max_deposit' => 500000,
        'min_withdrawal' => 5000,
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        foreach ($this->defaults as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            $this->settings[$key] = $setting ? $setting->value : $value;
        }

        if (is_string($this->settings['open_registration'])) {
            $this->settings['open_registration'] = $this->settings['open_registration'] === 'true';
        }
        if (is_string($this->settings['email_verification'])) {
            $this->settings['email_verification'] = $this->settings['email_verification'] === 'true';
        }
        if (is_string($this->settings['kyc_required'])) {
            $this->settings['kyc_required'] = $this->settings['kyc_required'] === 'true';
        }
        if (is_string($this->settings['maintenance_mode'])) {
            $this->settings['maintenance_mode'] = $this->settings['maintenance_mode'] === 'true';
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function toggleSetting($key)
    {
        $this->settings[$key] = ! $this->settings[$key];
    }

    public function saveSettings()
    {
        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? 'true' : 'false') : $value]
            );
        }

        session()->flash('message', 'Configuración guardada correctamente');
    }

    public function render()
    {
        return view('livewire.ajustes')->extends('layouts.dashboard');
    }
}
