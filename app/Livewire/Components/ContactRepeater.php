<?php

namespace App\Livewire\Components;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class ContactRepeater extends Component
{
    #[Modelable]
    public array $contacts = [];

    public string $fieldName = 'contacts';

    public array $types = [
        'whatsapp' => '💬 WhatsApp',
        'telegram' => '✈️ Telegram',
        'instagram' => '📷 Instagram',
        'facebook' => '📘 Facebook',
        'phone' => '📞 Teléfono',
    ];

    public function mount(string $fieldName = 'contacts')
    {
        $this->fieldName = $fieldName;
    }

    public function addContact()
    {
        $this->contacts[] = [
            'type' => 'whatsapp',
            'value' => '',
            'has_message' => false,
            'message' => '',
        ];
    }

    public function removeContact(int $index)
    {
        unset($this->contacts[$index]);
        $this->contacts = array_values($this->contacts);
    }

    public function render()
    {
        return view('livewire.components.contact-repeater');
    }
}
