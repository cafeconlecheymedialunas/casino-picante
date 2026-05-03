<?php

namespace App\Livewire;

use App\Models\Line;
use App\Models\LineAgent;
use Livewire\Component;

class Lineas extends Component
{
    public $showModal = false;

    public $editingLine = null;

    public $search = '';

    public $name = '';

    public $phone = '';

    public $whatsapp = '';

    public $telegram = '';

    public $status = 'active';

    public $type = 'whatsapp';

    protected $rules = [
        'name' => 'required|min:2',
        'phone' => 'nullable',
        'whatsapp' => 'nullable',
        'telegram' => 'nullable',
        'status' => 'required|in:active,inactive',
        'type' => 'required|in:whatsapp,telegram,phone',
    ];

    public function toggleLine($id)
    {
        $line = Line::find($id);
        $line->update(['status' => $line->status === 'active' ? 'inactive' : 'active']);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $line = Line::find($id);
        $this->editingLine = $line;
        $this->name = $line->name;
        $this->phone = $line->phone ?? '';
        $this->whatsapp = $line->whatsapp ?? '';
        $this->telegram = $line->telegram ?? '';
        $this->status = $line->status;
        $this->type = $line->type ?? 'whatsapp';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingLine = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->phone = '';
        $this->whatsapp = '';
        $this->telegram = '';
        $this->status = 'active';
        $this->type = 'whatsapp';
    }

    public function saveLine()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'telegram' => $this->telegram,
            'status' => $this->status,
            'type' => $this->type,
        ];

        if ($this->editingLine) {
            $this->editingLine->update($data);
            session()->flash('message', 'Línea actualizada correctamente');
        } else {
            Line::create($data);
            session()->flash('message', 'Línea creada correctamente');
        }

        $this->closeModal();
    }

    public function deleteLine($id)
    {
        Line::find($id)->delete();
        session()->flash('message', 'Línea eliminada correctamente');
    }

    public function getLines()
    {
        $query = Line::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('phone', 'like', '%'.$this->search.'%')
                ->orWhere('whatsapp', 'like', '%'.$this->search.'%');
        }

        return $query->orderBy('id')->get();
    }

    public function render()
    {
        $lines = $this->getLines();

        // Eager-load agent count per line to avoid N+1
        $agentCounts = LineAgent::whereIn('line_id', $lines->pluck('id'))
            ->where('is_active', true)
            ->selectRaw('line_id, count(*) as total')
            ->groupBy('line_id')
            ->pluck('total', 'line_id');

        return view('livewire.lineas', compact('lines', 'agentCounts'))->layout('layouts.dashboard');
    }
}
