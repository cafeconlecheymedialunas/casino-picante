<?php

namespace App\Livewire\Components;

use Livewire\Component;

class PageHeader extends Component
{
    public $title = '';

    public $subtitle = '';

    public $buttonText = '';

    public $buttonAction = '';

    public $showButton = true;

    public function render()
    {
        return view('livewire.components.page-header');
    }
}
