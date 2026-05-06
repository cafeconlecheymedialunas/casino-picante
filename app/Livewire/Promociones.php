<?php

namespace App\Livewire;

use App\Models\Platform;
use App\Models\Promotion;
use App\Services\NotificationService;
use App\Traits\HasLinePermissions;
use Carbon\Carbon;
use Livewire\Component;

class Promociones extends Component
{
    use HasLinePermissions;

    public $filter = 'all';

    public $selectedPromo = null;

    public $showModal = false;

    public $editingPromo = null;

    // Create/edit modal fields
    public $title = '';

    public $description = '';

    public $code = '';

    public $icon = '🎁';

    public $platform_id = null;

    public $type = 'bonus';

    public $bonus_percent = 0;

    public $bonus_amount = 0;

    public $min_deposit = 0;

    public $max_bonus = 0;

    public $start_date = '';

    public $end_date = '';

    public $status = 'draft';

    public $is_recurring = false;

    public $recurring_days = [];

    // Inline edit panel fields
    public $editTitle = '';

    public $editDescription = '';

    public $editCode = '';

    public $editIcon = '🎁';

    public $editStartDate = '';

    public $editEndDate = '';

    public $editStatus = 'draft';

    protected $rules = [
        'title' => 'required|min:3',
        'description' => 'nullable',
        'code' => 'nullable|string',
        'icon' => 'nullable|string',
        'type' => 'required|in:bonus,deposit,free_spin,promo',
        'bonus_percent' => 'nullable|numeric|min:0',
        'bonus_amount' => 'nullable|numeric|min:0',
        'min_deposit' => 'nullable|numeric|min:0',
        'max_bonus' => 'nullable|numeric|min:0',
        'start_date' => 'required',
        'end_date' => 'required',
        'status' => 'required|in:draft,published,hidden',
    ];

    public function selectPromo($id)
    {
        $this->selectedPromo = Promotion::find($id);
        if ($this->selectedPromo) {
            $this->editTitle = $this->selectedPromo->title;
            $this->editDescription = $this->selectedPromo->description ?? '';
            $this->editCode = $this->selectedPromo->code ?? '';
            $this->editIcon = $this->selectedPromo->icon ?? '🎁';
            $this->editStartDate = $this->selectedPromo->start_date?->format('Y-m-d\TH:i') ?? '';
            $this->editEndDate = $this->selectedPromo->end_date?->format('Y-m-d\TH:i') ?? '';
            $this->editStatus = $this->selectedPromo->status;
        }
    }

    public function openCreateModal()
    {
        $this->checkLinePermission('promo.create');

        $this->resetForm();
        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->end_date = Carbon::now()->addWeek()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEditModal($promoId)
    {
        $promo = Promotion::withoutGlobalScopes()->find($promoId);
        $this->editingPromo = $promo;
        $this->title = $promo->title;
        $this->description = $promo->description ?? '';
        $this->code = $promo->code ?? '';
        $this->icon = $promo->icon ?? '🎁';
        $this->platform_id = $promo->platform_id ?? '';
        $this->type = $promo->type;
        $this->bonus_percent = $promo->bonus_percent ?? 0;
        $this->bonus_amount = $promo->bonus_amount ?? 0;
        $this->min_deposit = $promo->min_deposit ?? 0;
        $this->max_bonus = $promo->max_bonus ?? 0;
        $this->start_date = $promo->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $promo->end_date?->format('Y-m-d') ?? '';
        $this->status = $promo->status;
        $this->is_recurring = $promo->is_recurring ?? false;
        $this->recurring_days = $promo->recurring_days ?? [];
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingPromo = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->code = '';
        $this->icon = '🎁';
        $this->platform_id = '';
        $this->type = 'bonus';
        $this->bonus_percent = 0;
        $this->bonus_amount = 0;
        $this->min_deposit = 0;
        $this->max_bonus = 0;
        $this->start_date = '';
        $this->end_date = '';
        $this->status = 'draft';
        $this->is_recurring = false;
        $this->recurring_days = [];
    }

    public function generateCode()
    {
        $this->code = strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public function generateEditCode()
    {
        $this->editCode = strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public function savePromo()
    {
        if ($this->editingPromo) {
            $this->checkLinePermission('promo.update');
        } else {
            $this->checkLinePermission('promo.create');
        }

        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'code' => $this->code,
            'icon' => $this->icon,
            'type' => $this->type,
            'status' => $this->status,
            'start_date' => Carbon::parse($this->start_date),
            'end_date' => Carbon::parse($this->end_date),
            'platform_id' => $this->platform_id ?: null,
        ];

        if ($this->type === 'bonus' || $this->type === 'deposit') {
            $data['bonus_percent'] = $this->bonus_percent;
            $data['bono_amount'] = $this->bonus_amount;
            $data['min_deposit'] = $this->min_deposit;
            $data['max_bonus'] = $this->max_bonus;
        }

        if ($this->editingPromo) {
            $this->editingPromo->update($data);
            session()->flash('message', 'Promoción actualizada correctamente');

            NotificationService::info(
                title: 'Promoción actualizada',
                message: "La promoción {$this->editingPromo->title} fue actualizada.",
                agentId: null,
                link: '/promociones',
                module: 'promotions'
            );
        } else {
            $data['line_id'] = session('active_line_id');
            $promo = Promotion::create($data);
            session()->flash('message', 'Promoción creada correctamente');

            NotificationService::success(
                title: 'Nueva promoción creada',
                message: "La promoción {$promo->title} fue creada exitosamente.",
                agentId: null,
                link: '/promociones',
                module: 'promotions'
            );
        }

        $this->closeModal();
    }

    public function saveEditPanel()
    {
        $this->checkLinePermission('promo.update');

        if (! $this->selectedPromo) {
            return;
        }

        $this->selectedPromo->update([
            'title' => $this->editTitle,
            'description' => $this->editDescription,
            'code' => $this->editCode ?: null,
            'icon' => $this->editIcon,
            'start_date' => $this->editStartDate ? Carbon::parse($this->editStartDate) : null,
            'end_date' => $this->editEndDate ? Carbon::parse($this->editEndDate) : null,
            'status' => $this->editStatus,
        ]);

        $this->selectedPromo = $this->selectedPromo->fresh();
        session()->flash('message', 'Promoción actualizada correctamente');
    }

    public function updateStatus($status)
    {
        $this->checkLinePermission('promo.update');

        if ($this->selectedPromo) {
            $this->selectedPromo->update(['status' => $status]);
            $this->editStatus = $status;
            $this->selectedPromo = $this->selectedPromo->fresh();
        }
    }

    public function deletePromo($promoId)
    {
        $promo = Promotion::withoutGlobalScopes()->find($promoId);
        $promoTitle = $promo->title;
        $promo->delete();

        if ($this->selectedPromo && $this->selectedPromo->id === $promoId) {
            $this->selectedPromo = null;
        }

        session()->flash('message', 'Promoción eliminada correctamente');

        NotificationService::danger(
            title: 'Promoción eliminada',
            message: "La promoción {$promoTitle} fue eliminada del sistema.",
            agentId: null,
            link: '/promociones',
            module: 'promotions'
        );
    }

    public function toggleStatus($promoId)
    {
        $this->checkLinePermission('promo.update');

        $promo = Promotion::withoutGlobalScopes()->find($promoId);
        $newStatus = $promo->status === 'published' ? 'draft' : 'published';
        $promo->update(['status' => $newStatus]);

        NotificationService::warning(
            title: 'Estado de promoción cambiado',
            message: "La promoción {$promo->title} fue ".($newStatus === 'published' ? 'publicada' : 'puesta en borrador').'.',
            agentId: null,
            link: '/promociones',
            module: 'promotions'
        );
    }

    public function getPlatforms()
    {
        if ($lineId = session('active_line_id')) {
            $line = Line::find($lineId);

            return $line ? $line->activePlatforms : collect();
        }

        return Platform::where('is_active', true)->orderBy('name')->get();
    }

    public function getPromotions()
    {
        $this->checkLinePermission('promo.read');

        $query = Promotion::query();

        if ($lineId = session('active_line_id')) {
            $query->where('line_id', $lineId);
        }

        if ($this->filter !== 'all') {
            $now = Carbon::now();
            switch ($this->filter) {
                case 'active':
                    $query->where('status', 'published')
                        ->where('start_date', '<=', $now)
                        ->where(function ($q) use ($now) {
                            $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                        });
                    break;
                case 'upcoming':
                    $query->where('status', 'published')->where('start_date', '>', $now);
                    break;
                case 'ended':
                    $query->where(function ($q) use ($now) {
                        $q->where('status', '!=', 'published')
                            ->orWhere(function ($q2) use ($now) {
                                $q2->whereNotNull('end_date')->where('end_date', '<', $now);
                            });
                    });
                    break;
                case 'draft':
                    $query->where('status', 'draft');
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        $promotions = $this->getPromotions();
        $platforms = $this->getPlatforms();

        return view('livewire.promociones', compact('promotions', 'platforms'))->layout('layouts.dashboard');
    }
}
