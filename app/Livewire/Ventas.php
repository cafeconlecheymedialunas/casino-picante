<?php

namespace App\Livewire;

use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Sale;
use App\Services\SalesStats;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Illuminate\Support\Collection;
use Livewire\Component;

class Ventas extends Component
{
    use HasLinePermissions, SendsNotifications;

    protected array $dispatchesEvents = [
        'page-header-action' => 'handlePageHeaderAction',
    ];

    public string $search = '';

    public string $lineFilter = 'all';

    public int $monthFilter;

    public int $yearFilter;

    public bool $showModal = false;

    public ?int $editingSaleId = null;

    public string $saleLineId = '';

    public string $salePlatformId = '';

    public int $saleMes;

    public int $saleAnio;

    public string $saleFecha = '';

    public string $saleDescripcion = '';

    public string $saleMontoFichas = '';

    public function mount(): void
    {
        $this->authorizeSalesAccess();
        $this->monthFilter = now()->month;
        $this->yearFilter = now()->year;
        $this->resetSaleForm();
    }

    public function openCreateModal(): void
    {
        $this->authorizeSalesAccess();
        $this->resetSaleForm();
        $this->showModal = true;
    }

    public function handlePageHeaderAction(string $action): void
    {
        if ($action === 'openCreateModal') {
            $this->openCreateModal();
        }
    }

    public function openEditModal(int $saleId): void
    {
        $sale = Sale::with('line')->findOrFail($saleId);
        $this->authorizeLineEdit($sale->line);

        $this->editingSaleId = $sale->id;
        $this->saleLineId = (string) $sale->line_id;
        $this->salePlatformId = (string) $sale->platform_id;
        $this->saleMes = $sale->fecha->month;
        $this->saleAnio = $sale->fecha->year;
        $this->saleFecha = $sale->fecha->format('Y-m-d');
        $this->saleDescripcion = $sale->descripcion ?? '';
        $this->saleMontoFichas = (string) $sale->monto_fichas;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetSaleForm();
    }

    public function saveSale(): void
    {
        $this->validate([
            'saleLineId' => 'required|integer|exists:lines,id',
            'salePlatformId' => 'required|integer|exists:platforms,id',
            'saleMes' => 'required|integer|min:1|max:12',
            'saleAnio' => 'required|integer|min:2020|max:2100',
            'saleFecha' => 'required|date',
            'saleDescripcion' => 'nullable|string|max:255',
            'saleMontoFichas' => 'required|numeric|min:0',
        ]);

        $line = Line::with(['platforms', 'lineAgents.agent'])->findOrFail((int) $this->saleLineId);
        $this->authorizeLineEdit($line);

        if (! $line->platforms()->where('platforms.id', (int) $this->salePlatformId)->exists()) {
            $this->addError('salePlatformId', 'La plataforma no pertenece a esta linea.');

            return;
        }

        $amount = (float) $this->saleMontoFichas;
        $percent = (float) $line->lineAgents()
            ->where('role', LineRoles::ENCARGADO)
            ->value('porcentaje_ganancia');

        $data = [
            'line_id' => $line->id,
            'platform_id' => (int) $this->salePlatformId,
            'fecha' => $this->saleFecha,
            'descripcion' => trim($this->saleDescripcion) ?: null,
            'monto_fichas' => $amount,
            'ganancia_superagente' => $amount * ($percent / 100),
        ];

        if ($this->editingSaleId) {
            Sale::where('line_id', $line->id)->findOrFail($this->editingSaleId)->update($data);
        } else {
            Sale::create($data);
        }

        $this->monthFilter = $this->saleMes;
        $this->yearFilter = $this->saleAnio;
        session()->flash('message', 'Venta guardada. Las estadisticas ya fueron recalculadas.');

        $this->notify(
            $this->editingSaleId ? 'Venta actualizada' : 'Venta cargada',
            'Se '.($this->editingSaleId ? 'actualizo' : 'cargo').' una venta para la linea '.$line->name.'.',
            'sales',
            '/ventas',
            'success'
        );

        $this->closeModal();
    }

    public function deleteSale(int $saleId): void
    {
        $sale = Sale::with('line')->findOrFail($saleId);
        $this->authorizeLineEdit($sale->line);
        $lineName = $sale->line?->name ?? 'Sin linea';
        $sale->delete();
        session()->flash('message', 'Venta eliminada. Las estadisticas se actualizaron.');

        $this->notify(
            'Venta eliminada',
            'Se elimino una venta de la linea '.$lineName.'.',
            'sales',
            '/ventas',
            'danger'
        );
    }

    public function updatedSaleLineId(): void
    {
        $this->salePlatformId = '';
    }

    public function updatedSaleMes(): void
    {
        $this->syncSaleDate();
    }

    public function updatedSaleAnio(): void
    {
        $this->syncSaleDate();
    }

    public function availableLines(): Collection
    {
        $query = Line::with(['platforms', 'lineAgents.agent']);

        if (! $this->isAdminMode()) {
            $query->whereHas('lineAgents', fn ($inner) => $inner
                ->where('agent_id', session('active_agent_id'))
                ->where('is_active', true));

            $query->whereIn('id', $this->editableLineIds());
        }

        return $query->orderBy('name')->get();
    }

    public function formPlatforms(): Collection
    {
        if (! $this->saleLineId) {
            return collect();
        }

        $line = $this->availableLines()->firstWhere('id', (int) $this->saleLineId);

        return $line
            ? ($line->relationLoaded('platforms') ? $line->getRelation('platforms') : $line->platforms()->get())
            : collect();
    }

    public function sales()
    {
        $lineIds = $this->availableLines()->pluck('id');

        return Sale::with(['line', 'platform'])
            ->whereIn('line_id', $lineIds)
            ->whereMonth('fecha', $this->monthFilter)
            ->whereYear('fecha', $this->yearFilter)
            ->when($this->lineFilter !== 'all', fn ($query) => $query->where('line_id', $this->lineFilter))
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($inner) use ($search) {
                    $inner->whereHas('line', fn ($line) => $line->where('name', 'like', $search))
                        ->orWhereHas('platform', fn ($platform) => $platform->where('name', 'like', $search));
                });
            })
            ->orderByDesc('fecha')
            ->orderBy('line_id')
            ->get();
    }

    public function stats(): array
    {
        return SalesStats::globalMonthStats($this->availableLines(), $this->monthFilter, $this->yearFilter);
    }

    public function monthLabel(int $month, int $year): string
    {
        $name = Sale::getMeses()[$month] ?? $month;

        return "{$name} {$year}";
    }

    public function render()
    {
        $this->authorizeSalesAccess();

        return view('livewire.ventas', [
            'lines' => $this->availableLines(),
            'formPlatforms' => $this->formPlatforms(),
            'sales' => $this->sales(),
            'stats' => $this->stats(),
            'months' => Sale::getMeses(),
        ])->layout('layouts.dashboard');
    }

    private function resetSaleForm(): void
    {
        $now = now();
        $this->editingSaleId = null;
        $this->saleLineId = '';
        $this->salePlatformId = '';
        $this->saleMes = $now->month;
        $this->saleAnio = $now->year;
        $this->saleFecha = $now->format('Y-m-d');
        $this->saleDescripcion = '';
        $this->saleMontoFichas = '';
        $this->syncSaleDate();
        $this->resetValidation();
    }

    private function syncSaleDate(): void
    {
        $date = now()->setDate($this->saleAnio, $this->saleMes, 1);
        $this->saleFecha = $date->format('Y-m-d');
    }

    private function authorizeLineEdit(?Line $line): void
    {
        if (! $line) {
            abort(404);
        }

        if ($this->isAdminMode()) {
            return;
        }

        $canEdit = LineAgent::where('line_id', $line->id)
            ->where('agent_id', session('active_agent_id'))
            ->where('is_active', true)
            ->exists();

        $hasPermission = LineAgentPermission::where('line_id', $line->id)
            ->where('agent_id', session('active_agent_id'))
            ->where('permission', Permissions::LINE_EDIT)
            ->exists();

        if (! $canEdit || ! $hasPermission) {
            abort(403, 'Necesitas permiso de edicion de linea para cargar ventas.');
        }
    }

    private function authorizeSalesAccess(): void
    {
        if ($this->isAdminMode()) {
            return;
        }

        $canAccess = $this->editableLineIds()->isNotEmpty();

        if (! $canAccess) {
            abort(403, 'Necesitas permiso de edicion de linea para ver ventas.');
        }
    }

    private function editableLineIds(): Collection
    {
        return LineAgentPermission::where('agent_id', session('active_agent_id'))
            ->where('permission', Permissions::LINE_EDIT)
            ->whereIn('line_id', LineAgent::where('agent_id', session('active_agent_id'))
                ->where('is_active', true)
                ->select('line_id'))
            ->pluck('line_id');
    }
}
