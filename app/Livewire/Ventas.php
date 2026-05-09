<?php

namespace App\Livewire;

use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Platform;
use App\Models\Sale;
use App\Models\User;
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

    public int $monthFilter;

    public int $yearFilter;

    public bool $showModal = false;

    public ?int $editingSaleId = null;

    public string $saleLineId = '';

    public string $saleAgentId = '';

    public string $saleClientId = '';

    public string $salePlatformId = '';

    public string $saleFecha = '';

    public string $saleDescripcion = '';

    public string $saleMontoFichas = '';

    public Collection $formAgents;

    public Collection $formClients;

    public Collection $formPlatforms;

    public function mount(?int $lineId = null): void
    {
        $this->authorizeSalesAccess();
        $this->monthFilter = now()->month;
        $this->yearFilter = now()->year;

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
        $sale = Sale::with(['line', 'agent', 'client', 'platform'])->findOrFail($saleId);
        $this->authorizeLineEdit($sale->line);

        $this->editingSaleId = $sale->id;
        $this->saleLineId = (string) $sale->line_id;
        $this->saleAgentId = $sale->agent_id ? (string) $sale->agent_id : '';
        $this->saleClientId = $sale->client_id ? (string) $sale->client_id : '';
        $this->salePlatformId = (string) $sale->platform_id;
        $this->saleFecha = $sale->fecha->format('Y-m-d');
        $this->saleDescripcion = $sale->descripcion ?? '';
        $this->saleMontoFichas = (string) $sale->monto_fichas;
        $this->updatedSaleFecha();
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
        $this->formAgents = $this->formAgents();
        $this->formClients = $this->formClients();
        $this->formPlatforms = $this->formPlatforms();
    }

    public function saveSale(): void
    {
        $this->validate([
            'saleLineId' => 'required|integer|exists:lines,id',
            'saleAgentId' => 'nullable|integer|exists:agents,id',
            'saleClientId' => 'nullable|integer|exists:users,id',
            'salePlatformId' => 'nullable|integer|exists:platforms,id',
            'saleFecha' => 'required|date',
            'saleDescripcion' => 'nullable|string|max:255',
            'saleMontoFichas' => 'required|numeric|min:0',
        ]);

        $line = Line::with(['platforms', 'lineAgents.agent'])->findOrFail((int) $this->saleLineId);
        $this->authorizeLineEdit($line);

        if ($this->salePlatformId && ! $line->platforms()->where('platforms.id', (int) $this->salePlatformId)->exists()) {
            $this->addError('salePlatformId', 'La plataforma no pertenece a esta linea.');

            return;
        }

        $amount = (float) $this->saleMontoFichas;
        $percent = (float) $line->lineAgents()
            ->where('role', LineRoles::ENCARGADO)
            ->value('porcentaje_ganancia');

        $data = [
            'line_id' => $line->id,
            'agent_id' => $this->saleAgentId ? (int) $this->saleAgentId : null,
            'client_id' => $this->saleClientId ? (int) $this->saleClientId : null,
            'platform_id' => $this->salePlatformId ? (int) $this->salePlatformId : null,
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

        $saleDate = Carbon::parse($this->saleFecha);
        $this->monthFilter = $saleDate->month;
        $this->yearFilter = $saleDate->year;
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
        $this->saleAgentId = '';
        $this->saleClientId = '';
        $this->salePlatformId = '';
        $this->syncFormCollections();
    }

    public function updatedSaleFecha(): void {}

    public function updatedSaleMes(): void {}

    public function updatedSaleAnio(): void {}

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

        return \DB::table('line_platform')
            ->join('platforms', 'line_platform.platform_id', '=', 'platforms.id')
            ->where('line_platform.line_id', (int) $this->saleLineId)
            ->where('line_platform.is_active', true)
            ->select('platforms.*')
            ->get()
            ->map(function ($platform) {
                return new Platform((array) $platform);
            });
    }

    public function formAgents(): Collection
    {
        if (! $this->saleLineId) {
            return collect();
        }

        return LineAgent::with('agent')
            ->where('line_id', (int) $this->saleLineId)
            ->where('is_active', true)
            ->get()
            ->map(function ($lineAgent) {
                return $lineAgent;
            });
    }

    public function formClients(): Collection
    {
        if (! $this->saleLineId) {
            return collect();
        }

        return \DB::table('line_clients')
            ->join('users', 'line_clients.user_id', '=', 'users.id')
            ->where('line_clients.line_id', (int) $this->saleLineId)
            ->where('line_clients.is_active', true)
            ->select('users.*')
            ->get()
            ->map(function ($user) {
                return new User((array) $user);
            });
    }

    public function sales()
    {
        $lineIds = $this->availableLines()->pluck('id');

        return Sale::with(['line', 'agent', 'client', 'platform'])
            ->whereIn('line_id', $lineIds)
            ->whereMonth('fecha', $this->monthFilter)
            ->whereYear('fecha', $this->yearFilter)
            ->when($this->lineFilter !== 'all', fn ($query) => $query->where('line_id', $this->lineFilter))
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($inner) use ($search) {
                    $inner->whereHas('line', fn ($line) => $line->where('name', 'like', $search))
                        ->orWhereHas('agent', fn ($agent) => $agent->where('name', 'like', $search))
                        ->orWhereHas('client', fn ($client) => $client->where('name', 'like', $search))
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
        $this->saleAgentId = '';
        $this->saleClientId = '';
        $this->salePlatformId = '';
        $this->saleFecha = $now->format('Y-m-d');
        $this->saleDescripcion = '';
        $this->saleMontoFichas = '';
        $this->updatedSaleFecha();
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
