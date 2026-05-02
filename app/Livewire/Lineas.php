<?php

namespace App\Livewire;

use App\Models\Line;
use Livewire\Component;

class Lineas extends Component
{
    public $showModal = false;

    public $editingLine = null;

    public function toggleLine($id)
    {
        $line = Line::find($id);
        $line->update(['status' => $line->status === 'active' ? 'inactive' : 'active']);
    }

    public function openEditModal($id)
    {
        $this->editingLine = Line::find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingLine = null;
    }

    public function getLines()
    {
        return Line::orderBy('id')->get();
    }

    public function render()
    {
        $lines = $this->getLines();

        return view('livewire.lineas', compact('lines'))->extends('layouts.dashboard');
    }
}
