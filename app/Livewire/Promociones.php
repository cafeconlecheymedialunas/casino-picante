<?php

namespace App\Livewire;

use App\Models\Platform;
use App\Models\Promotion;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Livewire\Component;

class Promociones extends Component
{
    use HasLinePermissions, SendsNotifications;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $tab = 'all';

    public bool $showModal = false;

    public ?int $editingPromoId = null;

    public string $title = '';

    public string $code = '';

    public string $icon = '🎁';

    public string $type = 'promo';

    public string $description = '';

    public $start_date = '';

    public $end_date = '';

    public bool $is_recurring = false;

    public array $recurring_days = [];

    public string $status = 'draft';

    public function mount(): void
    {
        if (! $this->hasLinePermission(Permissions::PROMO_READ)) {
            abort(403);
        }
    }

    public function render()
    {
        $query = Promotion::query();

        if ($this->tab !== 'all') {
            $query->where('type', $this->tab);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            });
        }

        return view('livewire.promociones', [
            'promos' => $query->latest()->paginate(12),
            'platforms' => Platform::orderBy('name')->get(),
            'canCreate' => $this->hasLinePermission(Permissions::PROMO_CREATE),
        ])->layout('layouts.dashboard');
    }

    public function openCreateModal(): void
    {
        $this->checkLinePermission(Permissions::PROMO_CREATE);
        $this->resetForm();
        $this->editingPromoId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $promoId): void
    {
        $this->checkLinePermission(Permissions::PROMO_UPDATE);
        $promo = Promotion::findOrFail($promoId);
        $this->editingPromoId = $promoId;
        $this->title = $promo->title;
        $this->code = $promo->code ?? '';
        $this->icon = $promo->icon ?? '🎁';
        $this->type = $promo->type;
        $this->description = $promo->description ?? '';
        $this->start_date = $promo->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $promo->end_date?->format('Y-m-d') ?? '';
        $this->is_recurring = (bool) $promo->is_recurring;
        $this->recurring_days = $promo->recurring_days ?? [];
        $this->status = $promo->status;
        $this->showModal = true;
    }

    public function savePromo(): void
    {
        $this->checkLinePermission($this->editingPromoId ? Permissions::PROMO_UPDATE : Permissions::PROMO_CREATE);

        $data = $this->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'icon' => 'string|max:10',
            'type' => 'required|in:bonus,free_spin,deposit,promo',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_recurring' => 'boolean',
            'recurring_days' => 'array',
            'status' => 'required|in:draft,published,hidden',
        ]);

        if ($this->editingPromoId) {
            $promo = Promotion::findOrFail($this->editingPromoId);
            $promo->update($data);
            $this->notify('Promoción actualizada', "La promoción {$promo->title} fue actualizada.", 'promotions', '/promociones', 'info');
        } else {
            $data['line_id'] = session('active_line_id');
            $promo = Promotion::create($data);
            $this->notify('Nueva promoción creada', "La promoción {$promo->title} fue creada exitosamente.", 'promotions', '/promociones', 'success');
        }

        $this->closeModal();
    }

    public function deletePromo(int $promoId): void
    {
        $this->checkLinePermission(Permissions::PROMO_DELETE);
        $promo = Promotion::findOrFail($promoId);
        $promoTitle = $promo->title;
        $promo->delete();
        $this->notify('Promoción eliminada', "La promoción {$promoTitle} fue eliminada del sistema.", 'promotions', '/promociones', 'danger');
    }

    public function toggleStatus(int $promoId): void
    {
        $this->checkLinePermission(Permissions::PROMO_UPDATE);
        $promo = Promotion::findOrFail($promoId);
        $newStatus = $promo->status === 'published' ? 'draft' : 'published';
        $promo->update(['status' => $newStatus]);
        $this->notify('Estado de promoción cambiado', "La promoción {$promo->title} fue ".($newStatus === 'published' ? 'publicada' : 'puesta en borrador').'.', 'promotions', '/promociones', 'warning');
    }

    public function generateCode(): void
    {
        $this->code = strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public function resetForm(): void
    {
        $this->title = '';
        $this->code = '';
        $this->icon = '🎁';
        $this->type = 'promo';
        $this->description = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_recurring = false;
        $this->recurring_days = [];
        $this->status = 'draft';
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->editingPromoId = null;
    }
}
