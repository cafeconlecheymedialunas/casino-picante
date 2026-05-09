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
        'email' => '✉️ Email',
        'instagram' => '📷 Instagram',
        'facebook' => '📘 Facebook',
        'phone' => '📞 Teléfono',
    ];

    public array $messageTypes = ['whatsapp', 'instagram', 'email'];

    public function mount(string $fieldName = 'contacts')
    {
        $this->fieldName = $fieldName;
    }

    public function addContact()
    {
        $this->contacts[] = [
            'type' => 'whatsapp',
            'value' => '',
            'message' => '',
        ];
    }

    public function updatedContacts($value, $key)
    {
        if (str_ends_with($key, '.type')) {
            $parts = explode('.', $key);
            $index = null;

            foreach ($parts as $part) {
                if (is_numeric($part)) {
                    $index = (int) $part;
                    break;
                }
            }

            if ($index !== null && ! in_array($value, $this->messageTypes, true)) {
                $this->contacts[$index]['message'] = '';
                $this->contacts = array_values($this->contacts);
            }
        }
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
