<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Platform;
use App\Services\SalesStats;
use App\Support\ImageStorage;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class Lineas extends Component
{
    use HasLinePermissions, SendsNotifications, WithFileUploads;

    public string $search = '';

    public string $statusFilter = 'all';

    public bool $showModal = false;

    public bool $showDetailsModal = false;

    public ?int $editingLineId = null;

    public ?int $activeLineId = null;

    public string $editTab = 'info';

    // Line info fields
    public string $name = '';

    public string $status = 'active';

    public string $description = '';

    public array $linePermissions = [];

    public bool $showLinePermissionsEditor = false;

    public string $portada_url = '';

    public string $perfil_url = '';

    public $portadaUpload = null;

    public $perfilUpload = null;

    // Single encargado
    public string $encargadoId = '';

    public string $encargadoPercent = '0';

    public array $channels = [];

    public array $selectedPlatformIds = [];

    // Stats fields (manual entry)
    public string $bestMonth = '';

    public string $bestMonthTotal = '';

    public string $bestPlatform = '';

    public string $bestPlatformTotal = '';

    // Agent permission editor
    public ?int $editingAgentPermissionsId = null;

    public array $agentPermissions = [];

    public array $availablePermissions = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|min:2|max:160',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:500',
            'linePermissions' => 'array',
            'portadaUpload' => 'nullable|image|max:20480',
            'perfilUpload' => 'nullable|image|max:20480',
            'encargadoId' => 'nullable|integer|exists:agents,id',
            'encargadoPercent' => 'nullable|numeric|min:0|max:100',
            'channels' => 'array',
            'channels.*.type' => 'nullable|string|max:40',
            'channels.*.value' => 'nullable|string|max:500',
            'channels.*.name' => 'nullable|string|max:100',
            'selectedPlatformIds' => 'array',
            'selectedPlatformIds.*' => 'integer|exists:platforms,id',
        ];
    }

    public function mount(int $id = 0): void
    {
        $editId = $id ?: (int) request()->query('edit');
        if ($editId) {
            $line = Line::find($editId);
            if ($line && $this->canManageLine($line)) {
                $this->openEditModal($editId);
            }
        }
    }

    public function openCreateModal(): void
    {
        if (! $this->isAdminMode()) {
            abort(403, 'Solo el administrador puede crear líneas.');
        }
        $this->resetForm();
        $this->editTab = 'info';
        $this->showModal = true;
    }

    public function openEditModal(int $lineId): void
    {
        $line = Line::with(['lineAgents.agent', 'platforms'])->findOrFail($lineId);
        $this->authorizeLineEdit($line);
        $this->fillForm($line);
        $this->editTab = 'info';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $lineId = $this->editingLineId;
        $this->showModal = false;
        $this->editingLineId = null;
        $this->activeLineId = null;
        $this->closeAgentPermissions();
        $this->resetForm();

        if ($lineId && request()->routeIs('lineas.edit')) {
            $this->redirect(route('lineas.detail', $lineId));
        }
    }

    public function switchTab(string $tab): void
    {
        $this->editTab = $tab;
        $this->closeAgentPermissions();
        $this->showLinePermissionsEditor = false;
    }

    public function openLinePermissionsEditor(): void
    {
        $this->showLinePermissionsEditor = true;
    }

    public function closeLinePermissionsEditor(): void
    {
        $this->showLinePermissionsEditor = false;
    }

    public function updatedPortadaUpload(): void
    {
        $this->validateOnly('portadaUpload');
    }

    public function updatedPerfilUpload(): void
    {
        $this->validateOnly('perfilUpload');
    }

    public function saveLine(): void
    {
        if ($this->editingLineId) {
            $this->authorizeLineEdit(Line::findOrFail($this->editingLineId));
        } else {
            if (! $this->isAdminMode()) {
                abort(403, 'Solo el administrador puede crear líneas.');
            }
        }

        $this->validate();

        $portadaPath = $this->portada_url;
        $perfilPath = $this->perfil_url;

        if ($this->portadaUpload) {
            $portadaPath = ImageStorage::store(
                $this->portadaUpload,
                'lineas/portadas',
                $this->portada_url ?: null
            );
        }

        if ($this->perfilUpload) {
            $perfilPath = ImageStorage::store(
                $this->perfilUpload,
                'lineas/perfiles',
                $this->perfil_url ?: null
            );
        }

        $encargadoId = $this->encargadoId !== '' ? (int) $this->encargadoId : null;
        $percent = (float) $this->encargadoPercent;

        $data = [
            'name' => trim($this->name),
            'status' => $this->status,
            'type' => 'whatsapp',
            'description' => trim($this->description) ?: null,
            'permissions' => empty($this->linePermissions) ? null : array_values($this->linePermissions),
            'encargado_id' => $encargadoId,
            'portada_url' => $portadaPath ?: null,
            'perfil_url' => $perfilPath ?: null,
            'contact_links' => $this->normalizedChannels(),
            'porcentaje_encargado' => $percent,
        ];

        $line = $this->editingLineId
            ? tap(Line::findOrFail($this->editingLineId))->update($data)
            : Line::create($data);

        if ($encargadoId) {
            $this->syncEncargado($line, $encargadoId, $percent);
        }
        $this->syncPlatforms($line);

        // Enforce hierarchical inheritance: if the line has an explicit permissions list,
        // remove any agent permissions that are no longer allowed by the line.
        if (is_array($this->linePermissions)) {
            if (empty($this->linePermissions)) {
                // Explicit empty list => remove all agent permissions for the line
                LineAgentPermission::where('line_id', $line->id)->delete();
            } else {
                LineAgentPermission::where('line_id', $line->id)
                    ->whereNotIn('permission', $this->linePermissions)
                    ->delete();
            }
        }

        $isEdit = (bool) $this->editingLineId;

        session()->flash(
            'message',
            $isEdit ? 'Linea actualizada correctamente.' : 'Linea creada correctamente.'
        );

        if ($encargadoId) {
            $this->notifyLineEncargados(
                $line,
                $isEdit ? 'Linea asignada actualizada' : 'Nueva linea asignada',
                'Tenes acceso como encargado a la linea '.$line->name.'.',
                $isEdit ? 'info' : 'success'
            );
        }

        $this->closeModal();
    }

    // Save only the line permissions from the edit form and synchronize agent permissions
    public function saveLinePermissions(): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        if (! $this->editingLineId) {
            return;
        }

        $permissionsToSave = array_values($this->linePermissions);

        Line::whereKey($this->editingLineId)->update(['permissions' => $permissionsToSave]);

        // Remove any agent permissions that are no longer allowed by the line
        $deleteQuery = LineAgentPermission::where('line_id', $this->editingLineId);
        if (empty($permissionsToSave)) {
            // Explicit empty list => remove all agent permissions for this line
            $deleteQuery->delete();
        } else {
            $deleteQuery->whereNotIn('permission', $permissionsToSave)->delete();
        }

        // Refresh local form state
        $this->fillForm(Line::findOrFail($this->editingLineId));

        session()->flash('message', 'Permisos de línea guardados y sincronizados.');
    }

    public function reloadLineForm(): void
    {
        if (! $this->editingLineId) {
            return;
        }

        $line = Line::findOrFail($this->editingLineId);
        $this->fillForm($line);
    }

    public function toggleLine(int $lineId): void
    {
        $line = Line::findOrFail($lineId);
        $this->authorizeLineEdit($line);

        $line->update([
            'status' => $line->status === 'active' ? 'inactive' : 'active',
        ]);

        $line->refresh();

        $this->notify(
            'Estado de linea cambiado',
            'La linea '.$line->name.' fue '.($line->status === 'active' ? 'activada' : 'pausada').'.',
            'lines',
            '/lineas',
            'warning'
        );

        $this->notifyLineEncargados(
            $line,
            'Estado de tu linea cambiado',
            'La linea '.$line->name.' fue '.($line->status === 'active' ? 'activada' : 'pausada').'.',
            'warning'
        );
    }

    public function deleteLine(int $lineId): void
    {
        if (! $this->isAdminMode()) {
            abort(403, 'Solo el administrador puede eliminar líneas.');
        }

        $line = Line::findOrFail($lineId);
        $line->delete();

        session()->flash('message', 'Línea eliminada correctamente.');
        $this->closeModal();
    }

    public function deleteImage(string $field): void
    {
        if ($this->editingLineId) {
            $this->authorizeLineEdit(Line::findOrFail($this->editingLineId));
        } else {
            $this->checkLinePermission(Permissions::LINE_EDIT);
        }

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

    // ── Agent permissions ──────────────────────────────────────────────────────

    public function openAgentPermissions(int $lineAgentId): void
    {
        $lineAgent = LineAgent::with('line')->findOrFail($lineAgentId);
        $this->authorizeLineEdit($lineAgent->line);

        // Prevent agents from editing their own permissions here unless admin
        $currentAgentId = session('active_agent_id') ? (int) session('active_agent_id') : null;
        if (! $this->isAdminMode() && $currentAgentId && $lineAgent->agent_id === $currentAgentId) {
            session()->flash('error', 'No podés editar tus propios permisos desde la edición de la línea.');

            return;
        }

        $this->editingAgentPermissionsId = $lineAgent->id;

        // Available = what the line has enabled (or full catalog if not set)
        $linePerms = $lineAgent->line->permissions;
        $this->availablePermissions = (is_array($linePerms) && ! empty($linePerms))
            ? $linePerms
            : LineAgentPermission::allPermissions();

        // If acting as encargado (not admin) further restrict to encargado's own perms
        if (! $this->isAdminMode()) {
            $encargadoLA = LineAgent::where('line_id', $lineAgent->line_id)
                ->where('role', LineRoles::ENCARGADO)
                ->first();

            if ($encargadoLA) {
                $encargadoPerms = $encargadoLA->getPermissionsListAttribute();
                $this->availablePermissions = array_values(
                    array_intersect($this->availablePermissions, $encargadoPerms)
                );
            }
        }

        // Default to all available if agent has no permissions set yet
        $current = $lineAgent->getPermissionsListAttribute();
        $this->agentPermissions = empty($current) ? $this->availablePermissions : $current;
    }

    public function saveAgentPermissions(): void
    {
        $lineAgent = LineAgent::with('line')->findOrFail($this->editingAgentPermissionsId);
        $this->authorizeLineEdit($lineAgent->line);

        // Recompute allowed permissions server-side to avoid client tampering
        $linePermsRaw = $lineAgent->line->permissions ?? null;
        $linePerms = is_array($linePermsRaw) ? $linePermsRaw : LineAgentPermission::allPermissions();

        if ($this->isAdminMode()) {
            $available = $linePerms;
        } else {
            $encargadoLA = LineAgent::where('line_id', $lineAgent->line_id)
                ->where('role', LineRoles::ENCARGADO)
                ->first();
            if ($encargadoLA) {
                $encPerms = $encargadoLA->getPermissionsListAttribute();
                $available = array_values(array_intersect($linePerms, $encPerms));
            } else {
                $available = $linePerms;
            }
        }

        $filtered = array_values(array_intersect($this->agentPermissions, $available));

        // Ensure filtered is array
        $filtered = is_array($filtered) ? $filtered : [];

        $lineAgent->syncPermissions($filtered);

        // If we're updating the encargado, remove from other agents any permissions not allowed by the new encargado set
        if ($lineAgent->role === LineRoles::ENCARGADO) {
            $deleteQuery = LineAgentPermission::where('line_id', $lineAgent->line_id)
                ->where('agent_id', '!=', $lineAgent->agent_id);

            if (empty($filtered)) {
                // Explicit empty => delete all other agents' permissions
                $deleted = $deleteQuery->delete();
                \Log::info('Lineas::saveAgentPermissions deleted all other agents permissions', [
                    'line_id' => $lineAgent->line_id,
                    'encargado_agent_id' => $lineAgent->agent_id,
                    'deleted_rows' => $deleted,
                    'filtered' => $filtered,
                ]);
            } else {
                $deleted = $deleteQuery->whereNotIn('permission', $filtered)->delete();
                \Log::info('Lineas::saveAgentPermissions deleted permissions not in encargado set', [
                    'line_id' => $lineAgent->line_id,
                    'encargado_agent_id' => $lineAgent->agent_id,
                    'deleted_rows' => $deleted,
                    'filtered' => $filtered,
                ]);
            }

            // Refresh related models to ensure UI reads latest data
            $lineAgent->line->refresh();
        }

        session()->flash('message', 'Permisos actualizados correctamente.');

        $this->closeAgentPermissions();
    }

    public function closeAgentPermissions(): void
    {
        $this->editingAgentPermissionsId = null;
        $this->agentPermissions = [];
        $this->availablePermissions = [];
    }

    public function addAgent(int $agentId): void
    {
        $line = Line::findOrFail($this->editingLineId);
        $this->authorizeLineEdit($line);

        $result = LineAgent::firstOrCreate(
            ['line_id' => $line->id, 'agent_id' => $agentId],
            ['role' => LineRoles::MIEMBRO, 'is_active' => true]
        );

        session()->flash(
            'message',
            $result->wasRecentlyCreated
                ? 'Agente agregado a la linea.'
                : 'El agente ya estaba asignado a esta linea.'
        );
    }

    public function removeLineAgent(int $lineAgentId): void
    {
        $lineAgent = LineAgent::with('line')->findOrFail($lineAgentId);
        $this->authorizeLineEdit($lineAgent->line);

        if ($lineAgent->role === LineRoles::ENCARGADO) {
            session()->flash('message', 'No se puede eliminar el encargado desde aquí. Cambialo en la pestaña Encargado.');

            return;
        }

        LineAgentPermission::where('line_id', $lineAgent->line_id)
            ->where('agent_id', $lineAgent->agent_id)
            ->delete();

        $lineAgent->delete();

        $this->closeAgentPermissions();
    }

    public function openEditSaleInModal(int $saleId): void
    {
        $line = Line::findOrFail($this->editingLineId);
        $this->authorizeLineEdit($line);

        $sale = Sale::where('line_id', $line->id)->findOrFail($saleId);
        $this->editingSaleId = $sale->id;
        $this->salePlatformId = $sale->platform_id;
        $this->saleFechaInicio = $sale->fecha_inicio ? $sale->fecha_inicio->format('Y-m-d') : '';
        $this->saleFechaFin = $sale->fecha_fin ? $sale->fecha_fin->format('Y-m-d') : '';
        $this->saleDescripcion = $sale->descripcion ?? '';
        $this->saleMontoFichas = (string) $sale->monto_fichas;
    }

    public function saveSale(): void
    {
        $line = Line::with(['platforms', 'lineAgents'])
            ->findOrFail($this->activeLineId);

        $this->authorizeLineEdit($line);

        $this->validate([
            'salePlatformId' => 'required|integer|min:1|exists:platforms,id',
            'saleDate' => 'required|date',
            'saleDescripcion' => 'nullable|string|max:255',
            'saleMontoFichas' => 'required|numeric|min:0',
        ]);

        if (! $line->platforms()->where('platforms.id', $this->salePlatformId)->exists()) {
            $this->addError('salePlatformId', 'La plataforma no pertenece a esta linea.');

            return;
        }

        $monto = (float) $this->saleMontoFichas;
        $percent = (float) $line->lineAgents()
            ->where('role', LineRoles::ENCARGADO)
            ->value('porcentaje_ganancia');

        $data = [
            'line_id' => $line->id,
            'platform_id' => $this->salePlatformId,
            'fecha' => $this->saleDate,
            'descripcion' => $this->saleDescripcion ?: null,
            'monto_fichas' => $monto,
            'ganancia_superagente' => $monto * ($percent / 100),
        ];

        if ($this->editingSaleId) {
            Sale::where('line_id', $line->id)
                ->findOrFail($this->editingSaleId)
                ->update($data);
        } else {
            Sale::create($data);
        }

        session()->flash('message', 'Venta registrada correctamente.');

        $this->notify(
            'Venta registrada',
            "Se registró una venta para la línea {$line->name}.",
            'sales',
            '/lineas',
            'success'
        );

        if ($this->showModal) {
            $this->resetSalesForm();
        } else {
            $this->closeSalesModal();
        }
    }

    public function deleteSale(int $saleId): void
    {
        $sale = Sale::with('line')->findOrFail($saleId);
        $this->authorizeLineEdit($sale->line);

        $lineName = $sale->line->name;
        $sale->delete();

        session()->flash('message', 'Venta eliminada correctamente.');

        $this->notify(
            'Venta eliminada',
            "Se eliminó una venta de la línea {$lineName}.",
            'sales',
            '/lineas',
            'danger'
        );
    }

    // ── Details view ───────────────────────────────────────────────────────────

    public function openDetailsModal(int $lineId): void
    {
        $line = Line::findOrFail($lineId);
        $this->authorizeLineView($line);

        $this->activeLineId = $lineId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->activeLineId = null;
    }

    public function editFromDetail(): void
    {
        $lineId = $this->activeLineId;
        $this->closeDetailsModal();
        $this->openEditModal($lineId);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function canManageLine(Line $line): bool
    {
        if ($this->isAdminMode()) {
            return true;
        }

        return LineAgent::where('line_id', $line->id)
            ->where('agent_id', session('active_agent_id'))
            ->where('role', LineRoles::ENCARGADO)
            ->exists();
    }

    public function render()
    {
        $lines = $this->lines();

        $detailLine = ($this->activeLineId && $this->showDetailsModal)
            ? Line::with(['lineAgents.agent', 'platforms', 'sales.platform'])->find($this->activeLineId)
            : null;

        $editLineAgents = ($this->editingLineId && $this->editTab === 'agentes')
            ? LineAgent::with('agent')
                ->where('line_id', $this->editingLineId)
                ->where('role', '!=', LineRoles::ENCARGADO)
                ->orderBy('id')
                ->get()
            : collect();

        $assignedAgentIds = $editLineAgents->pluck('agent_id')->toArray();

        $availableAgents = ($this->editingLineId && $this->editTab === 'agentes')
            ? Agent::where('status', 'active')
                ->whereNotIn('id', $assignedAgentIds)
                ->orderBy('name')
                ->get()
            : collect();

        return view('livewire.lineas', [
            'activeLines' => $lines->where('status', 'active'),
            'inactiveLines' => $lines->where('status', 'inactive'),
            'linesTotal' => $lines->count(),
            'availableEncargados' => $this->availableEncargados(),
            'allPlatforms' => Platform::where('is_active', true)->orderBy('name')->get(),
            'detailLine' => $detailLine,
            'editLineAgents' => $editLineAgents,
            'availableAgents' => $availableAgents,
            'permissionCatalog' => Permissions::catalog(),
        ])->layout('layouts.dashboard');
    }

    public function statsFor(Line $line): array
    {
        return SalesStats::lineStats($line);
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->name = '';
        $this->status = 'active';
        $this->description = '';
        $this->linePermissions = [];
        $this->showLinePermissionsEditor = false;
        $this->portada_url = '';
        $this->perfil_url = '';
        $this->portadaUpload = null;
        $this->perfilUpload = null;
        $this->editTab = 'info';
        $this->encargadoId = '';
        $this->encargadoPercent = '0';

        $this->channels = [['type' => 'whatsapp', 'value' => '', 'name' => '']];
        $this->selectedPlatformIds = [];
        $this->linePermissions = LineAgentPermission::allPermissions();

        $this->resetValidation();
    }

    private function fillForm(Line $line): void
    {
        $this->editingLineId = $line->id;
        $this->name = $line->name;
        $this->status = $line->status === 'inactive' ? 'inactive' : 'active';
        $this->description = $line->description ?? '';
        $this->linePermissions = is_array($line->permissions) ? $line->permissions : [];
        $this->showLinePermissionsEditor = false;
        $this->portada_url = $line->portada_url ?? '';
        $this->perfil_url = $line->perfil_url ?? '';

        $encargado = $line->lineAgents->firstWhere('role', LineRoles::ENCARGADO);
        $this->encargadoId = (string) ($encargado?->agent_id ?? $line->encargado_id ?? '');
        $this->encargadoPercent = (string) ($encargado?->porcentaje_ganancia ?? $line->porcentaje_encargado ?? 0);

        $this->channels = $this->mapChannels($line->contact_links ?? []);

        if (empty($this->channels)) {
            $this->channels = [['type' => 'whatsapp', 'value' => '', 'name' => '']];
        }

        $this->selectedPlatformIds = $line->platforms()->pluck('platforms.id')->map(fn ($id) => (string) $id)->toArray();
    }

    private function lines(): Collection
    {
        $query = Line::with(['lineAgents.agent', 'sales.platform'])
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', $search)
                        ->orWhereHas(
                            'lineAgents.agent',
                            fn ($a) => $a->where('name', 'like', $search)->orWhere('email', 'like', $search)
                        )
                        ->orWhereHas(
                            'platforms',
                            fn ($p) => $p->where('name', 'like', $search)
                        );
                });
            })
            ->when(
                $this->statusFilter !== 'all',
                fn ($q) => $q->where('status', $this->statusFilter)
            );

        if (! $this->isAdminMode()) {
            $query->whereHas(
                'lineAgents',
                fn ($inner) => $inner->where('agent_id', session('active_agent_id'))
            );
        }

        return $query
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
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
            ->map(fn ($r) => [
                'type' => trim($r['type'] ?? 'other'),
                'value' => trim($r['value'] ?? ''),
                'name' => trim($r['name'] ?? ''),
            ])
            ->filter(fn ($r) => $r['value'] !== '')
            ->values()
            ->toArray();
    }

    private function syncEncargado(Line $line, int $agentId, float $percent): void
    {
        LineAgent::where('line_id', $line->id)
            ->where('role', LineRoles::ENCARGADO)
            ->where('agent_id', '!=', $agentId)
            ->delete();

        LineAgent::updateOrCreate(
            ['line_id' => $line->id, 'agent_id' => $agentId],
            ['role' => LineRoles::ENCARGADO, 'is_active' => true, 'porcentaje_ganancia' => $percent]
        );
    }

    private function syncPlatforms(Line $line): void
    {
        $sync = array_fill_keys(
            array_map('intval', $this->selectedPlatformIds),
            ['is_active' => true]
        );

        $line->platforms()->sync($sync);
    }

    private function mapChannels(array $links): array
    {
        return collect($links)
            ->map(fn ($l) => [
                'type' => $l['type'] ?? 'other',
                'value' => $l['value'] ?? $l['url'] ?? '',
                'name' => $l['name'] ?? '',
            ])
            ->values()
            ->toArray();
    }

    private function notifyLineEncargados(Line $line, string $title, string $message, string $type = 'info'): void
    {
        $currentAgentId = session('active_agent_id') ? (int) session('active_agent_id') : null;

        LineAgent::where('line_id', $line->id)
            ->where('role', LineRoles::ENCARGADO)
            ->pluck('agent_id')
            ->unique()
            ->each(function ($agentId) use ($currentAgentId, $title, $message, $type) {
                $agentId = (int) $agentId;
                if ($agentId !== $currentAgentId) {
                    $this->notifyAgent($agentId, $title, $message, 'lines', '/lineas', $type);
                }
            });
    }

    private function authorizeLineEdit(Line $line): void
    {
        if (! $this->canManageLine($line)) {
            abort(403, 'Solo el administrador o el encargado puede editar esta linea.');
        }
    }

    private function authorizeLineView(Line $line): void
    {
        if ($this->isAdminMode()) {
            return;
        }

        $canView = LineAgent::where('line_id', $line->id)
            ->where('agent_id', session('active_agent_id'))
            ->where('is_active', true)
            ->exists();

        if (! $canView) {
            abort(403, 'No podes ver lineas fuera de tu alcance.');
        }
    }
}
