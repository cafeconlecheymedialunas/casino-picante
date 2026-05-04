<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ContactRepeater extends Component
{
    public $contacts = [];

    public string $fieldName = 'contacts';

    public array $types = [
        'whatsapp' => '💬 WhatsApp',
        'telegram' => '✈️ Telegram',
        'instagram' => '📷 Instagram',
        'facebook' => '📘 Facebook',
        'phone' => '📞 Teléfono',
    ];

    public function mount(array $contacts = [], string $fieldName = 'contacts')
    {
        $this->contacts = $contacts;
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
