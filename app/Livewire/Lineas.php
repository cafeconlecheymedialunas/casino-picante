<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\Platform;
use App\Models\Sale;
use App\Services\NotificationService;
use App\Services\SalesStats;
use App\Support\ImageStorage;
use App\Traits\HasLinePermissions;
use Illuminate\Str;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class Lineas extends Component
{
    use HasLinePermissions;
    use WithFileUploads;

    public string $search = '';

    public string $statusFilter = 'all';

    public bool $showModal = false;

    public bool $showSalesModal = false;

    public bool $showDetailsModal = false;

    public ?int $editingLineId = null;

    public ?int $activeLineId = null;

    public string $name = '';

    public string $status = 'active';

    public string $portada_url = '';

    public string $perfil_url = '';

    public $portadaUpload = null;

    public $perfilUpload = null;

    public string $encargadoId = '';

    public string $encargadoPercent = '0';

    public array $channels = [];

    public array $platformRows = [];

    public ?int $editingSaleId = null;

    public int $salePlatformId = 0;

    public int $saleMes = 0;

    public int $saleAnio = 0;

    public string $saleFechaInicio = '';

    public string $saleFechaFin = '';

    public string $saleMontoFichas = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|min:2|max:160',
            'status' => 'required|in:active,inactive',
            'portadaUpload' => 'nullable|image|max:4096',
            'perfilUpload' => 'nullable|image|max:4096',
            'encargadoId' => 'required|integer|exists:agents,id',
            'encargadoPercent' => 'nullable|numeric|min:0|max:100',
            'channels' => 'array',
            'channels.*.name' => 'nullable|string|max:80',
            'channels.*.url' => 'nullable|string|max:500',
            'platformRows' => 'array',
            'platformRows.*.name' => 'nullable|string|max:100',
            'platformRows.*.url' => 'nullable|string|max:500',
        ];
    }

    public function openCreateModal(): void
    {
        $this->checkLinePermission('line.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $lineId): void
    {
        $line = Line::with(['lineAgents.agent', 'platforms'])->findOrFail($lineId);
        $this->authorizeLineEdit($line);
        $this->fillForm($line);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingLineId = null;
        $this->resetForm();
    }

    public function saveLine(): void
    {
        $this->editingLineId
            ? $this->authorizeLineEdit(Line::findOrFail($this->editingLineId))
            : $this->checkLinePermission('line.create');

        $this->validate();

        $encargadoId = (int) $this->encargadoId;
        $percent = (float) $this->encargadoPercent;

        $portadaPath = $this->portada_url;
        $perfilPath = $this->perfil_url;

        if ($this->portadaUpload) {
            $portadaPath = ImageStorage::store($this->portadaUpload, 'lineas/portadas', $this->portada_url ?: null);
        }

        if ($this->perfilUpload) {
            $perfilPath = ImageStorage::store($this->perfilUpload, 'lineas/perfiles', $this->perfil_url ?: null);
        }

        $data = [
            'name' => trim($this->name),
            'status' => $this->status,
            'type' => 'whatsapp',
            'encargado_id' => $encargadoId,
            'portada_url' => $portadaPath ?: null,
            'perfil_url' => $perfilPath ?: null,
            'contact_links' => $this->normalizedChannels(),
            'porcentaje_encargado' => $percent,
        ];

        $line = $this->editingLineId
            ? tap(Line::findOrFail($this->editingLineId))->update($data)
            : Line::create($data);

        $this->syncEncargado($line, $encargadoId, $percent);
        $this->syncPlatforms($line);

        $isEdit = (bool) $this->editingLineId;

        session()->flash('message', $isEdit ? 'Linea actualizada correctamente.' : 'Linea creada correctamente.');

        if ($isEdit) {
            NotificationService::info(
                title: 'Línea actualizada',
                message: "La línea {$line->name} fue actualizada.",
                agentId: null,
                link: '/lineas',
                module: 'lines'
            );
        } else {
            NotificationService::success(
                title: 'Nueva línea creada',
                message: "La línea {$line->name} fue creada exitosamente.",
                agentId: null,
                link: '/lineas',
                module: 'lines'
            );
        }

        $this->closeModal();
    }

    public function deleteLine(int $lineId): void
    {
        $line = Line::findOrFail($lineId);
        $this->authorizeLineEdit($line);
        $lineName = $line->name;
        $line->delete();
        session()->flash('message', 'Linea eliminada correctamente.');

        NotificationService::danger(
            title: 'Línea eliminada',
            message: "La línea {$lineName} fue eliminada del sistema.",
            agentId: null,
            link: '/lineas',
            module: 'lines'
        );
    }

    public function toggleLine(int $lineId): void
    {
        $line = Line::findOrFail($lineId);
        $this->authorizeLineEdit($line);
        $status = $line->status === 'active' ? 'inactive' : 'active';
        $line->update(['status' => $status]);

        NotificationService::warning(
            title: 'Estado de línea cambiado',
            message: "La línea {$line->name} fue ".($status === 'active' ? 'activada' : 'desactivada').'.',
            agentId: null,
            link: '/lineas',
            module: 'lines'
        );
    }

    public function addChannel(): void
    {
        $this->channels[] = ['name' => '', 'url' => ''];
    }

    public function removeChannel(int $index): void
    {
        unset($this->channels[$index]);
        $this->channels = array_values($this->channels);
    }

    public function addPlatformRow(): void
    {
        $this->platformRows[] = ['name' => '', 'url' => ''];
    }

    public function removePlatformRow(int $index): void
    {
        unset($this->platformRows[$index]);
        $this->platformRows = array_values($this->platformRows);
    }

    public function removeImage(string $field): void
    {
        if ($field === 'portada') {
            if ($this->editingLineId && $this->portada_url) {
                ImageStorage::delete($this->portada_url);
                Line::whereKey($this->editingLineId)->update(['portada_url' => null]);
            }

            $this->portadaUpload = null;
            $this->portada_url = '';
        }

        if ($field === 'perfil') {
            if ($this->editingLineId && $this->perfil_url) {
                ImageStorage::delete($this->perfil_url);
                Line::whereKey($this->editingLineId)->update(['perfil_url' => null]);
            }

            $this->perfilUpload = null;
            $this->perfil_url = '';
        }
    }

    public function openSalesModal(int $lineId, ?int $saleId = null): void
    {
        $line = Line::with('platforms')->findOrFail($lineId);
        $this->authorizeLineEdit($line);
        $this->activeLineId = $line->id;
        $this->resetSalesForm();

        if ($saleId) {
            $sale = Sale::where('line_id', $line->id)->findOrFail($saleId);
            $this->editingSaleId = $sale->id;
            $this->salePlatformId = $sale->platform_id;
            $this->saleMes = $sale->mes;
            $this->saleAnio = $sale->anio;
            $this->saleFechaInicio = $sale->fecha_inicio->format('Y-m-d');
            $this->saleFechaFin = $sale->fecha_fin->format('Y-m-d');
            $this->saleMontoFichas = (string) $sale->monto_fichas;
        }

        $this->showSalesModal = true;
    }

    public function closeSalesModal(): void
    {
        $this->showSalesModal = false;
        $this->resetSalesForm();
    }

    public function saveSale(): void
    {
        $line = Line::with(['platforms', 'lineAgents'])->findOrFail($this->activeLineId);
        $this->authorizeLineEdit($line);

        $this->validate([
            'salePlatformId' => 'required|integer|min:1|exists:platforms,id',
            'saleMes' => 'required|integer|min:1|max:12',
            'saleAnio' => 'required|integer|min:2020|max:2100',
            'saleFechaInicio' => 'required|date',
            'saleFechaFin' => 'required|date|after_or_equal:saleFechaInicio',
            'saleMontoFichas' => 'required|numeric|min:0',
        ]);

        if (! $line->platforms()->where('platforms.id', $this->salePlatformId)->exists()) {
            $this->addError('salePlatformId', 'La plataforma no pertenece a esta linea.');

            return;
        }

        $monto = (float) $this->saleMontoFichas;
        $percent = (float) $line->lineAgents()
            ->where('role', 'encargado')
            ->value('porcentaje_ganancia');

        $data = [
            'line_id' => $line->id,
            'platform_id' => $this->salePlatformId,
            'mes' => $this->saleMes,
            'anio' => $this->saleAnio,
            'fecha_inicio' => $this->saleFechaInicio,
            'fecha_fin' => $this->saleFechaFin,
            'monto_fichas' => $monto,
            'ganancia_superagente' => $monto * ($percent / 100),
        ];

        if ($this->editingSaleId) {
            Sale::where('line_id', $line->id)->findOrFail($this->editingSaleId)->update($data);
        } else {
            Sale::updateOrCreate([
                'line_id' => $line->id,
                'platform_id' => $this->salePlatformId,
                'mes' => $this->saleMes,
                'anio' => $this->saleAnio,
            ], $data);
        }

        session()->flash('message', 'Venta registrada correctamente.');

        NotificationService::success(
            title: 'Venta registrada',
            message: "Se registró una venta para la línea {$line->name}.",
            agentId: null,
            link: '/lineas',
            module: 'lines'
        );

        $this->closeSalesModal();
    }

    public function deleteSale(int $saleId): void
    {
        $sale = Sale::with('line')->findOrFail($saleId);
        $this->authorizeLineEdit($sale->line);
        $lineName = $sale->line->name;
        $sale->delete();
        session()->flash('message', 'Venta eliminada correctamente.');

        NotificationService::danger(
            title: 'Venta eliminada',
            message: "Se eliminó una venta de la línea {$lineName}.",
            agentId: null,
            link: '/lineas',
            module: 'lines'
        );
    }

    public function openDetailsModal(int $lineId): void
    {
        $this->activeLineId = $lineId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->activeLineId = null;
    }

    public function canManageLine(Line $line): bool
    {
        if ($this->isAdminMode()) {
            return true;
        }

        return LineAgent::where('line_id', $line->id)
            ->where('agent_id', session('active_agent_id'))
            ->where('role', 'encargado')
            ->exists();
    }

    public function render()
    {
        $lines = $this->lines();
        $detailLine = $this->activeLineId
            ? Line::with(['lineAgents.agent', 'platforms', 'sales.platform'])->find($this->activeLineId)
            : null;
        $salesLine = $this->activeLineId
            ? Line::with(['platforms', 'sales.platform'])->find($this->activeLineId)
            : null;

        return view('livewire.lineas', [
            'activeLines' => $lines->where('status', 'active'),
            'inactiveLines' => $lines->where('status', 'inactive'),
            'linesTotal' => $lines->count(),
            'availableEncargados' => $this->availableEncargados(),
            'detailLine' => $detailLine,
            'salesLine' => $salesLine,
            'months' => Sale::getMeses(),
        ])->layout('layouts.dashboard');
    }

    public function statsFor(Line $line): array
    {
        return SalesStats::lineStats($line);
    }

    public function monthLabel(int $month, int $year): string
    {
        $name = Sale::getMeses()[$month] ?? $month;

        return "{$name} {$year}";
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->status = 'active';
        $this->portada_url = '';
        $this->perfil_url = '';
        $this->portadaUpload = null;
        $this->perfilUpload = null;
        $this->encargadoId = '';
        $this->encargadoPercent = '0';
        $this->channels = [['name' => '', 'url' => '']];
        $this->platformRows = [['name' => '', 'url' => '']];
        $this->resetValidation();
    }

    private function fillForm(Line $line): void
    {
        $this->editingLineId = $line->id;
        $this->name = $line->name;
        $this->status = $line->status === 'inactive' ? 'inactive' : 'active';
        $this->portada_url = $line->portada_url ?? '';
        $this->perfil_url = $line->perfil_url ?? '';
        $this->channels = $this->mapChannels($line->contact_links ?? []);
        $this->platformRows = $line->platforms()->get()->map(fn (Platform $platform) => [
            'name' => $platform->name,
            'url' => $platform->pivot->custom_message ?: $platform->website_url,
        ])->values()->toArray();

        $encargado = $line->lineAgents->firstWhere('role', 'encargado');
        $this->encargadoId = (string) ($encargado?->agent_id ?? $line->encargado_id ?? '');
        $this->encargadoPercent = (string) ($encargado?->porcentaje_ganancia ?? $line->porcentaje_encargado ?? 0);

        if (empty($this->channels)) {
            $this->channels = [['name' => '', 'url' => '']];
        }

        if (empty($this->platformRows)) {
            $this->platformRows = [['name' => '', 'url' => '']];
        }
    }

    private function lines(): Collection
    {
        $query = Line::with(['lineAgents.agent', 'sales.platform'])
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', $search)
                        ->orWhereHas('lineAgents.agent', fn ($agent) => $agent->where('name', 'like', $search)->orWhere('email', 'like', $search))
                        ->orWhereHas('platforms', fn ($platform) => $platform->where('name', 'like', $search));
                });
            })
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter));

        if (! $this->isAdminMode()) {
            $query->whereHas('lineAgents', fn ($inner) => $inner
                ->where('agent_id', session('active_agent_id')));
        }

        return $query->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();
    }

    private function availableEncargados(): Collection
    {
        return Agent::where('status', 'active')
            ->where('cargo', 'super_agente')
            ->orderBy('name')
            ->get();
    }

    private function normalizedChannels(): array
    {
        return collect($this->channels)
            ->map(fn ($row) => [
                'name' => trim($row['name'] ?? ''),
                'url' => trim($row['url'] ?? ''),
            ])
            ->filter(fn ($row) => $row['name'] !== '' || $row['url'] !== '')
            ->values()
            ->toArray();
    }

    private function syncEncargado(Line $line, int $agentId, float $percent): void
    {
        LineAgent::where('line_id', $line->id)
            ->where('role', 'encargado')
            ->where('agent_id', '!=', $agentId)
            ->delete();

        LineAgent::updateOrCreate(
            ['line_id' => $line->id, 'agent_id' => $agentId],
            [
                'role' => 'encargado',
                'is_active' => true,
                'porcentaje_ganancia' => $percent,
            ]
        );
    }

    private function syncPlatforms(Line $line): void
    {
        $platforms = collect($this->platformRows)
            ->map(fn ($row) => [
                'name' => trim($row['name'] ?? ''),
                'url' => trim($row['url'] ?? ''),
            ])
            ->filter(fn ($row) => $row['name'] !== '' || $row['url'] !== '')
            ->values();

        $sync = [];

        foreach ($platforms as $row) {
            $name = $row['name'] !== '' ? $row['name'] : parse_url($row['url'], PHP_URL_HOST) ?? 'Plataforma';
            $baseSlug = Str::slug($name) ?: 'plataforma';
            $slug = $baseSlug;
            $suffix = 1;

            while (Platform::where('slug', $slug)->where('name', '!=', $name)->exists()) {
                $slug = $baseSlug.'-'.$suffix++;
            }

            $platform = Platform::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'website_url' => $row['url'] ?: null, 'is_active' => true]
            );

            if ($row['url'] !== '' && $platform->website_url !== $row['url']) {
                $platform->update(['website_url' => $row['url']]);
            }

            $sync[$platform->id] = [
                'custom_message' => $row['url'] ?: null,
                'is_active' => true,
            ];
        }

        $line->platforms()->sync($sync);
        $line->update(['platforms' => $platforms->toArray()]);
    }

    private function mapChannels(array $links): array
    {
        return collect($links)
            ->map(fn ($link) => [
                'name' => $link['name'] ?? $link['type'] ?? '',
                'url' => $link['url'] ?? $link['value'] ?? '',
            ])
            ->values()
            ->toArray();
    }

    private function resetSalesForm(): void
    {
        $now = now();
        $this->editingSaleId = null;
        $this->salePlatformId = 0;
        $this->saleMes = $now->month;
        $this->saleAnio = $now->year;
        $this->saleFechaInicio = $now->copy()->startOfMonth()->format('Y-m-d');
        $this->saleFechaFin = $now->copy()->endOfMonth()->format('Y-m-d');
        $this->saleMontoFichas = '';
        $this->resetValidation();
    }

    private function authorizeLineEdit(Line $line): void
    {
        if (! $this->canManageLine($line)) {
            abort(403, 'Solo el administrador o el encargado puede editar esta linea.');
        }
    }
}
