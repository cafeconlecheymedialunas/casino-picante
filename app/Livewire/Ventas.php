<?php

namespace App\Livewire;

use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Platform;
use App\Models\Sale;
use App\Services\SalesStats;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Carbon\Carbon;
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

    public string $dateInicioFilter = '';

    public string $dateFinFilter = '';

    public bool $showModal = false;

    public ?int $editingSaleId = null;

    public string $saleLineId = '';

    public string $salePlatformId = '';

    public string $saleFechaInicio = '';

    public string $saleFechaFin = '';

    public string $saleDescripcion = '';

    public string $saleMontoFichas = '';

    public Collection $formPlatforms;

    public function mount(?int $lineId = null): void
    {
        $this->authorizeSalesAccess();
        $now = now();
        $this->dateInicioFilter = $now->format('Y-m-d');
        $this->dateFinFilter = $now->format('Y-m-d');

        // Pre-select line if provided as parameter, otherwise use session
        if ($lineId) {
            $this->lineFilter = (string) $lineId;
        } elseif (session()->has('active_line_id')) {
            $this->lineFilter = (string) session('active_line_id');
        }

        $this->resetSaleForm();
        $this->syncFormCollections();
    }

    public function openCreateModal(): void
    {
        $this->authorizeSalesAccess();

        if ($this->lineFilter === 'all') {
            return;
        }

        $this->resetSaleForm();

        $this->saleLineId = $this->lineFilter;
        $this->syncFormCollections();

        $this->showModal = true;
    }

    public function openEditModal(int $saleId): void
    {
        $sale = Sale::with(['line', 'platform'])->findOrFail($saleId);
        $this->authorizeLineEdit($sale->line);

        $this->editingSaleId = $sale->id;
        $this->saleLineId = (string) $sale->line_id;
        $this->salePlatformId = (string) $sale->platform_id;
        $this->saleFechaInicio = $sale->fecha_inicio ? $sale->fecha_inicio->format('Y-m-d') : '';
        $this->saleFechaFin = $sale->fecha_fin ? $sale->fecha_fin->format('Y-m-d') : '';
        $this->saleDescripcion = $sale->descripcion ?? '';
        $this->saleMontoFichas = (string) $sale->monto_fichas;
        $this->syncFormCollections();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetSaleForm();
    }

    private function syncFormCollections(): void
    {
        $this->formPlatforms = $this->formPlatforms();
    }

    public function saveSale(): void
    {
        $this->validate([
            'saleLineId' => 'required|integer|exists:lines,id',
            'salePlatformId' => 'nullable|integer|exists:platforms,id',
            'saleFechaInicio' => 'required|date',
            'saleFechaFin' => 'required|date|after_or_equal:saleFechaInicio',
            'saleDescripcion' => 'nullable|string|max:255',
            'saleMontoFichas' => 'required|numeric|min:0',
        ]);

        $line = Line::with(['platforms', 'lineAgents.agent'])->findOrFail((int) $this->saleLineId);
        $this->authorizeLineEdit($line);

        $amount = (float) $this->saleMontoFichas;
        $percent = (float) $line->lineAgents()
            ->where('role', LineRoles::ENCARGADO)
            ->value('porcentaje_ganancia');

        $data = [
            'line_id' => $line->id,
            'platform_id' => $this->salePlatformId ? (int) $this->salePlatformId : null,
            'fecha_inicio' => $this->saleFechaInicio,
            'fecha_fin' => $this->saleFechaFin,
            'descripcion' => trim($this->saleDescripcion) ?: null,
            'monto_fichas' => $amount,
            'ganancia_superagente' => $amount * ($percent / 100),
        ];

        if ($this->editingSaleId) {
            Sale::where('line_id', $line->id)->findOrFail($this->editingSaleId)->update($data);
        } else {
            Sale::create($data);
        }

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
        return Platform::where('is_active', true)->orderBy('name')->get();
    }

    public function sales()
    {
        $lineIds = $this->availableLines()->pluck('id');

        $query = Sale::with(['line', 'platform'])
            ->whereIn('line_id', $lineIds)
            ->when($this->lineFilter !== 'all', fn ($query) => $query->where('line_id', $this->lineFilter))
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($inner) use ($search) {
                    $inner->whereHas('line', fn ($line) => $line->where('name', 'like', $search))
                        ->orWhereHas('platform', fn ($platform) => $platform->where('name', 'like', $search));
                });
            });

        if ($this->dateInicioFilter && $this->dateFinFilter) {
            $query->whereDate('fecha_inicio', '>=', $this->dateInicioFilter)
                ->whereDate('fecha_fin', '<=', $this->dateFinFilter);
        }

        return $query->orderByDesc('fecha_inicio')
            ->orderBy('line_id')
            ->get();
    }

    public function stats(): array
    {
        return SalesStats::globalDateRangeStats($this->availableLines(), $this->dateInicioFilter, $this->dateFinFilter);
    }

    public function dateRangeLabel(): string
    {
        if ($this->dateInicioFilter && $this->dateFinFilter) {
            return $this->dateInicioFilter.' al '.$this->dateFinFilter;
        }
        return 'Seleccionar rango de fechas';
    }

    public function render()
    {
        $this->authorizeSalesAccess();

        return view('livewire.ventas', [
            'lines' => $this->availableLines(),
            'sales' => $this->sales(),
            'stats' => $this->stats(),
        ])->layout('layouts.dashboard');
    }

    private function resetSaleForm(): void
    {
        $now = now();
        $this->editingSaleId = null;
        $this->saleLineId = '';
        $this->salePlatformId = '';
        $this->saleFechaInicio = $now->format('Y-m-d');
        $this->saleFechaFin = $now->format('Y-m-d');
        $this->saleDescripcion = '';
        $this->saleMontoFichas = '';
        $this->syncFormCollections();
        $this->resetValidation();
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
