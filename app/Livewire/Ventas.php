<?php

namespace App\Livewire;

use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Sale;
use App\Services\SalesStats;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Illuminate\Support\Collection;
use Livewire\Component;

class Ventas extends Component
{
    use HasLinePermissions, SendsNotifications;

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

    public string $saleFechaInicio = '';

    public string $saleFechaFin = '';

    public string $saleMontoFichas = '';

    public function mount(): void
    {
        $this->monthFilter = now()->month;
        $this->yearFilter = now()->year;
        $this->resetSaleForm();
    }

    public function openCreateModal(): void
    {
        $this->resetSaleForm();
        $this->showModal = true;
    }

    public function openEditModal(int $saleId): void
    {
        $sale = Sale::with('line')->findOrFail($saleId);
        $this->authorizeLineEdit($sale->line);

        $this->editingSaleId = $sale->id;
        $this->saleLineId = (string) $sale->line_id;
        $this->salePlatformId = (string) $sale->platform_id;
        $this->saleMes = $sale->mes;
        $this->saleAnio = $sale->anio;
        $this->saleFechaInicio = $sale->fecha_inicio->format('Y-m-d');
        $this->saleFechaFin = $sale->fecha_fin->format('Y-m-d');
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
            'saleFechaInicio' => 'required|date',
            'saleFechaFin' => 'required|date|after_or_equal:saleFechaInicio',
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
            ->where('role', 'encargado')
            ->value('porcentaje_ganancia');

        $data = [
            'line_id' => $line->id,
            'platform_id' => (int) $this->salePlatformId,
            'mes' => $this->saleMes,
            'anio' => $this->saleAnio,
            'fecha_inicio' => $this->saleFechaInicio,
            'fecha_fin' => $this->saleFechaFin,
            'monto_fichas' => $amount,
            'ganancia_superagente' => $amount * ($percent / 100),
        ];

        if ($this->editingSaleId) {
            Sale::where('line_id', $line->id)->findOrFail($this->editingSaleId)->update($data);
        } else {
            Sale::updateOrCreate([
                'line_id' => $line->id,
                'platform_id' => (int) $this->salePlatformId,
                'mes' => $this->saleMes,
                'anio' => $this->saleAnio,
            ], $data);
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
        $this->syncSaleDates();
    }

    public function updatedSaleAnio(): void
    {
        $this->syncSaleDates();
    }

    public function availableLines(): Collection
    {
        $query = Line::with(['platforms', 'lineAgents.agent']);

        if (! $this->isAdminMode()) {
            $query->whereHas('lineAgents', fn ($inner) => $inner
                ->where('agent_id', session('active_agent_id'))
                ->where('is_active', true));
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
            ->where('mes', $this->monthFilter)
            ->where('anio', $this->yearFilter)
            ->when($this->lineFilter !== 'all', fn ($query) => $query->where('line_id', $this->lineFilter))
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($inner) use ($search) {
                    $inner->whereHas('line', fn ($line) => $line->where('name', 'like', $search))
                        ->orWhereHas('platform', fn ($platform) => $platform->where('name', 'like', $search));
                });
            })
            ->orderByDesc('anio')
            ->orderByDesc('mes')
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
        $this->saleMontoFichas = '';
        $this->syncSaleDates();
        $this->resetValidation();
    }

    private function syncSaleDates(): void
    {
        $date = now()->setDate($this->saleAnio, $this->saleMes, 1);
        $this->saleFechaInicio = $date->copy()->startOfMonth()->format('Y-m-d');
        $this->saleFechaFin = $date->copy()->endOfMonth()->format('Y-m-d');
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
            ->where('role', 'encargado')
            ->where('is_active', true)
            ->exists();

        if (! $canEdit) {
            abort(403, 'Solo el administrador o el encargado puede cargar ventas.');
        }
    }
}
