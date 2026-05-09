<?php

namespace App\Livewire;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Platform;
use App\Models\Sale;
use App\Support\ImageStorage;
use App\Support\LineRoles;
use App\Support\Permissions;
use App\Traits\HasLinePermissions;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class LineDetail extends Component
{
    use HasLinePermissions;
    use WithFileUploads;

    public int $lineId;

    public Line $line;

    // Edit line modal
    public bool $showEditModal = false;

    public string $editName = '';

    public string $editType = 'whatsapp';

    public string $editPhone = '';

    public string $editDescription = '';

    public string $editIcon = '';

    public string $editStatus = 'active';

    // Contact links repeater
    public array $editContactLinks = []; // [{type: 'whatsapp', label: '', value: ''}, ...]

    // Platforms from catalog (line_platform pivot)
    public array $editPlatforms = []; // [['platform_id' => 1, 'custom_message' => ''], ...]

    // Available platforms from master catalog
    public function getAvailablePlatformsProperty()
    {
        return Platform::where('is_active', true)->orderBy('name')->get();
    }

    // Assign agent modal
    public bool $showAssignModal = false;

    public string $assignAgentSearch = '';

    public ?int $assignAgentId = null;

    public string $assignRole = LineRoles::MIEMBRO;

    // Encargado selection
    public $selectedEncargadoId = null;

    // Permissions panel
    public ?int $editingPermAgentId = null;

    public array $editingPerms = []; // ['promo.create' => true/false, ...]

    // Line permissions
    public array $linePermissionsList = [];

    // Inline editing mode
    public bool $isEditing = false;

    public $portadaUpload = null;

    public $perfilUpload = null;

    public function toggleInlineEdit(): void
    {
        $this->isEditing = ! $this->isEditing;
        if (! $this->isEditing) {
            // Just cancelled, reload original data
            $this->initInlineFields();
        }
    }

    public function saveAll(): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        $this->validate([
            'line.name' => 'required|string|max:255',
            'line.encargado_id' => 'nullable|integer|exists:agents,id',
            'line.porcentaje_encargado' => 'nullable|numeric|min:0|max:100',
            'line.ventas_mes_actual' => 'nullable|numeric|min:0',
            'line.ventas_mes_pasado' => 'nullable|numeric|min:0',
            'line.ventas_mes_antiguo' => 'nullable|numeric|min:0',
            'line.ganancia_encargado' => 'nullable|numeric|min:0',
            'line.mejor_mes_total' => 'nullable|numeric|min:0',
            'line.mejor_plataforma_total' => 'nullable|numeric|min:0',
            'portadaUpload' => 'nullable|image|max:20480',
            'perfilUpload' => 'nullable|image|max:20480',
        ]);

        if ($this->portadaUpload) {
            $this->line->portada_url = ImageStorage::store($this->portadaUpload, 'lineas/portadas', $this->line->portada_url);
        }

        if ($this->perfilUpload) {
            $this->line->perfil_url = ImageStorage::store($this->perfilUpload, 'lineas/perfiles', $this->line->perfil_url);
        }

        $this->line->save();
        $this->portadaUpload = null;
        $this->perfilUpload = null;
        $this->isEditing = false;
        session()->flash('message', 'Todos los cambios guardados.');
    }

    public function removeImageField(string $field): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        if ($field === 'portada') {
            ImageStorage::delete($this->line->portada_url);
            $this->portadaUpload = null;
            $this->line->portada_url = null;
        }

        if ($field === 'perfil') {
            ImageStorage::delete($this->line->perfil_url);
            $this->perfilUpload = null;
            $this->line->perfil_url = null;
        }
    }

    // Assign percentage to encargado
    public bool $showPercentageModal = false;

    public ?int $percentageAgentId = null;

    public string $percentageValue = '';

    // Image uploads
    public string $portadaUrl = '';

    // Edit toggles por sección
    public bool $editingInfo = false;

    public bool $editingContacts = false;

    public bool $editingEncargado = false;

    public bool $editingImages = false;

    // Campos editables en línea
    public string $inlineName = '';

    public string $inlineType = 'whatsapp';

    public string $inlineStatus = 'active';

    public string $inlineIcon = '';

    public string $inlineDescription = '';

    public string $inlinePortada = '';

    public string $inlinePerfil = '';

    public ?int $inlineEncargadoId = null;

    public array $inlineContacts = [];

    public function mount(int $id): void
    {
        $this->lineId = $id;
        $this->authorizeLineContext();
        $this->linePermissionsList = $this->line->permissions ?? [];
        $this->initInlineFields();
    }

    public function initInlineFields(): void
    {
        $this->inlineName = $this->line->name ?? '';
        $this->inlineType = $this->line->type ?? 'whatsapp';
        $this->inlineStatus = $this->line->status ?? 'active';
        $this->inlineIcon = $this->line->icon ?? '';
        $this->inlineDescription = $this->line->description ?? '';
        $this->inlinePortada = $this->line->portada_url ?? '';
        $this->inlinePerfil = $this->line->perfil_url ?? '';
        $this->inlineEncargadoId = $this->line->encargado_id;
        $this->inlineContacts = $this->line->contact_links ?? [];
        if (empty($this->inlineContacts)) {
            $this->inlineContacts = [['type' => 'whatsapp', 'value' => '', 'label' => 'WhatsApp']];
        }
    }

    // ── Toggle Edición por Sección ─────────────────────────────────────────

    public function toggleEditInfo(): void
    {
        if ($this->editingInfo) {
            $this->saveInfo();
        } else {
            $this->inlineName = $this->line->name ?? '';
            $this->inlineType = $this->line->type ?? 'whatsapp';
            $this->inlineStatus = $this->line->status ?? 'active';
            $this->inlineIcon = $this->line->icon ?? '';
            $this->inlineDescription = $this->line->description ?? '';
        }
        $this->editingInfo = ! $this->editingInfo;
    }

    public function saveInfo(): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);
        $this->line->update([
            'name' => $this->inlineName,
            'type' => $this->inlineType,
            'status' => $this->inlineStatus,
            'icon' => $this->inlineIcon,
            'description' => $this->inlineDescription,
            'portada_url' => $this->inlinePortada ?: null,
            'perfil_url' => $this->inlinePerfil ?: null,
        ]);
        $this->line->refresh();
        session()->flash('message', 'Información actualizada.');
    }

    public function toggleEditContacts(): void
    {
        if ($this->editingContacts) {
            $this->saveContacts();
        } else {
            $this->inlineContacts = $this->line->contact_links ?? [];
            if (empty($this->inlineContacts)) {
                $this->inlineContacts = [['type' => 'whatsapp', 'value' => '', 'label' => 'WhatsApp']];
            }
        }
        $this->editingContacts = ! $this->editingContacts;
    }

    public function saveContacts(): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);
        $contacts = array_filter($this->inlineContacts, fn ($c) => ! empty($c['value']));
        $this->line->update(['contact_links' => array_values($contacts)]);
        $this->line->refresh();
        session()->flash('message', 'Contactos actualizados.');
    }

    public function addInlineContact(): void
    {
        $this->inlineContacts[] = ['type' => 'whatsapp', 'value' => '', 'label' => 'Nuevo'];
    }

    public function removeInlineContact(int $index): void
    {
        unset($this->inlineContacts[$index]);
        $this->inlineContacts = array_values($this->inlineContacts);
    }

    public function toggleEditEncargado(): void
    {
        if ($this->editingEncargado) {
            $this->saveEncargado();
        } else {
            $this->inlineEncargadoId = $this->line->encargado_id;
        }
        $this->editingEncargado = ! $this->editingEncargado;
    }

    public function saveEncargado(): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);
        $this->line->update(['encargado_id' => $this->inlineEncargadoId ?: null]);
        $this->line->refresh();
        session()->flash('message', 'Encargado actualizado.');
    }

    public function toggleEditImages(): void
    {
        if ($this->editingImages) {
            $this->saveImages();
        } else {
            $this->inlinePortada = $this->line->portada_url ?? '';
            $this->inlinePerfil = $this->line->perfil_url ?? '';
        }
        $this->editingImages = ! $this->editingImages;
    }

    public function saveImages(): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);
        $this->line->update([
            'portada_url' => $this->inlinePortada ?: null,
            'perfil_url' => $this->inlinePerfil ?: null,
        ]);
        $this->line->refresh();
        session()->flash('message', 'Imágenes actualizadas.');
    }

    // ── Edit Line (Modal legacy) ───────────────────────────────────────────

    public function openEditModal(): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);
        $this->editName = $this->line->name;
        $this->editType = $this->line->type ?? 'whatsapp';
        $this->editPhone = $this->line->phone ?? '';
        $this->editDescription = $this->line->description ?? '';
        $this->editIcon = $this->line->icon ?? '';
        $this->editStatus = $this->line->status;

        // Load contact links (normalize to include 'message' and 'has_message' keys)
        $rawLinks = $this->line->contact_links ?? [
            ['type' => 'whatsapp', 'label' => 'WhatsApp', 'value' => $this->line->whatsapp ?? '', 'message' => $this->line->whatsapp_message ?? ''],
            ['type' => 'telegram', 'label' => 'Telegram', 'value' => $this->line->telegram ?? '', 'message' => $this->line->telegram_message ?? ''],
        ];
        $this->editContactLinks = array_map(function ($link) {
            return array_merge([
                'message' => '',
                'has_message' => false,
            ], $link);
        }, $rawLinks);

        // Load platforms from pivot table
        $this->editPlatforms = $this->line->platforms()->get()->map(function ($platform) {
            return [
                'platform_id' => $platform->id,
                'name' => $platform->name,
                'slug' => $platform->slug,
                'logo_url' => $platform->logo_url,
                'custom_message' => $platform->pivot->custom_message ?? '',
                'is_active' => $platform->pivot->is_active,
            ];
        })->toArray();

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
    }

    public function saveLineEdit(): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        $this->line->update([
            'name' => $this->editName,
            'type' => $this->editType,
            'phone' => $this->editPhone,
            'description' => $this->editDescription,
            'icon' => $this->editIcon,
            'contact_links' => $this->editContactLinks,
            'status' => $this->editStatus,
        ]);

        // Update platforms in pivot table
        $this->line->platforms()->detach();
        foreach ($this->editPlatforms as $p) {
            if (! empty($p['platform_id'])) {
                $this->line->platforms()->attach($p['platform_id'], [
                    'custom_message' => $p['custom_message'] ?? '',
                    'is_active' => $p['is_active'] ?? true,
                ]);
            }
        }

        $this->line->refresh();
        session()->flash('message', 'Línea actualizada.');
    }

    // ── Contact Links Repeater ────────────────────────────────

    public function addContactLink(): void
    {
        $this->editContactLinks[] = ['type' => 'whatsapp', 'label' => '', 'value' => '', 'message' => ''];
    }

    public function removeContactLink(int $index): void
    {
        unset($this->editContactLinks[$index]);
        $this->editContactLinks = array_values($this->editContactLinks);
    }

    // ── Platforms ────────────────────────────────────────

    public function togglePlatform(int $platformId): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        $found = false;
        foreach ($this->editPlatforms as &$p) {
            if ($p['platform_id'] == $platformId) {
                $p['is_active'] = ! ($p['is_active'] ?? true);
                $found = true;
            }
        }

        if (! $found) {
            $platform = Platform::find($platformId);
            if ($platform) {
                $this->editPlatforms[] = [
                    'platform_id' => $platformId,
                    'name' => $platform->name,
                    'slug' => $platform->slug,
                    'logo_url' => $platform->logo_url,
                    'custom_message' => '',
                    'is_active' => true,
                ];
            }
        }
    }

    public function updatePlatformMessage(int $platformId, string $message): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        foreach ($this->editPlatforms as &$p) {
            if ($p['platform_id'] == $platformId) {
                $p['custom_message'] = $message;
            }
        }
    }

    public function togglePlatformActivation($platformId): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        $platform = Platform::find($platformId);
        if (! $platform) {
            return;
        }

        $alreadyAttached = $this->line->platforms()->wherePivot('platform_id', $platformId)->exists();

        if ($alreadyAttached) {
            // toggle is_active
            $pivot = $this->line->platforms()->wherePivot('platform_id', $platformId)->first();
            $newStatus = ! $pivot->pivot->is_active;
            $this->line->platforms()->updateExistingPivot($platformId, ['is_active' => $newStatus]);
        } else {
            // attach as active
            $this->line->platforms()->attach($platformId, [
                'is_active' => true,
                'custom_message' => '',
            ]);
        }

        $this->line->refresh();
    }

    // ── Assign Agent ───────────────────────────────────────────────────────

    public function openAssignModal(): void
    {
        $this->checkLinePermission(Permissions::AGENT_ASSIGN);
        $this->assignAgentSearch = '';
        $this->assignAgentId = null;
        $this->assignRole = LineRoles::MIEMBRO;
        $this->showAssignModal = true;
    }

    public function getSearchAgentsProperty(): Collection
    {
        if (strlen($this->assignAgentSearch) < 2) {
            return collect();
        }

        $alreadyIn = LineAgent::where('line_id', $this->lineId)->pluck('agent_id');

        return Agent::where('status', 'active')
            ->whereNotIn('id', $alreadyIn)
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->assignAgentSearch}%")
                    ->orWhere('email', 'like', "%{$this->assignAgentSearch}%");
            })
            ->limit(8)
            ->get();
    }

    public function selectAssignAgent(int $agentId): void
    {
        $this->assignAgentId = $agentId;
        $this->assignAgentSearch = Agent::find($agentId)?->name ?? '';
    }

    public function confirmAssign(): void
    {
        $this->checkLinePermission(Permissions::AGENT_ASSIGN);

        if (! $this->assignAgentId) {
            return;
        }

        LineAgent::updateOrCreate(
            ['line_id' => $this->lineId, 'agent_id' => $this->assignAgentId],
            ['role' => $this->assignRole, 'is_active' => true]
        );

        $this->showAssignModal = false;
        session()->flash('message', 'Agente asignado correctamente.');
    }

    public function removeAgent(int $agentId): void
    {
        $this->checkLinePermission(Permissions::AGENT_ASSIGN);

        LineAgent::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->delete();

        LineAgentPermission::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->delete();

        if ($this->editingPermAgentId === $agentId) {
            $this->editingPermAgentId = null;
        }

        session()->flash('message', 'Agente removido de la línea.');
    }

    public function toggleAgentActive(int $agentId): void
    {
        $this->checkLinePermission(Permissions::AGENT_UPDATE);

        $la = LineAgent::where('line_id', $this->lineId)->where('agent_id', $agentId)->first();
        if ($la) {
            $la->update(['is_active' => ! $la->is_active]);
        }
    }

    public function changeAgentRole(int $agentId, string $role): void
    {
        $this->checkLinePermission(Permissions::AGENT_UPDATE);

        // Validate role is either 'encargado' or 'miembro'
        if (! in_array($role, [LineRoles::ENCARGADO, LineRoles::MIEMBRO])) {
            session()->flash('error', 'Rol inválido. Debe ser encargado o miembro.');

            return;
        }

        LineAgent::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->update(['role' => $role]);
    }

    // ── Permissions panel ──────────────────────────────────────────────────

    public function openPermissions(int $agentId): void
    {
        $this->checkLinePermission(Permissions::AGENT_PERMISSIONS);

        $this->editingPermAgentId = $agentId;

        // Get line permissions (max allowed)
        $linePerms = $this->line->permissions ?? [];

        // Get encargado permissions (if exists)
        $encargadoPerms = [];
        if ($this->line->encargado_id) {
            $encargadoPerms = LineAgentPermission::where('line_id', $this->lineId)
                ->where('agent_id', $this->line->encargado_id)
                ->pluck('permission')
                ->toArray();
        }

        // Get current agent permissions
        $granted = LineAgentPermission::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->pluck('permission')
            ->flip()
            ->toArray();

        // Build checkbox map
        $this->editingPerms = [];
        $isAdmin = $this->isAdminMode();
        foreach (LineAgentPermission::allPermissions() as $perm) {
            if ($isAdmin) {
                // Admin sees and can assign all permissions
                $this->editingPerms[$perm] = isset($granted[$perm]);
            } elseif ($this->canDelegate($perm)) {
                $allowedByLine = in_array($perm, $linePerms);
                $allowedByEncargado = empty($encargadoPerms) || in_array($perm, $encargadoPerms);
                if ($allowedByLine && $allowedByEncargado) {
                    $this->editingPerms[$perm] = isset($granted[$perm]);
                }
            }
        }
    }

    public function savePermissions(): void
    {
        $this->checkLinePermission(Permissions::AGENT_PERMISSIONS);

        if (! $this->editingPermAgentId) {
            return;
        }

        // Get line permissions (max allowed)
        $linePerms = $this->line->permissions ?? [];

        // Get encargado permissions (if exists)
        $encargadoPerms = [];
        if ($this->line->encargado_id) {
            $encargadoPerms = LineAgentPermission::where('line_id', $this->lineId)
                ->where('agent_id', $this->line->encargado_id)
                ->pluck('permission')
                ->toArray();
        }

        // Collect only the checked ones that are allowed (subset of line AND encargado permissions AND delegation rule)
        $toGrant = [];
        $isAdmin = $this->isAdminMode();
        foreach ($this->editingPerms as $perm => $checked) {
            if ($checked) {
                if ($isAdmin) {
                    $toGrant[] = $perm;
                } else {
                    $allowedByLine = in_array($perm, $linePerms);
                    $allowedByEncargado = empty($encargadoPerms) || in_array($perm, $encargadoPerms);
                    $allowedByDelegation = $this->canDelegate($perm);
                    if ($allowedByLine && $allowedByEncargado && $allowedByDelegation) {
                        $toGrant[] = $perm;
                    }
                }
            }
        }

        // Remove old permissions for this agent/line
        $deleteQuery = LineAgentPermission::where('line_id', $this->lineId)
            ->where('agent_id', $this->editingPermAgentId);
        if (! $isAdmin) {
            $delegatable = array_filter(LineAgentPermission::allPermissions(), fn ($p) => $this->canDelegate($p));
            $deleteQuery->whereIn('permission', $delegatable);
        }
        $deleteQuery->delete();

        foreach ($toGrant as $perm) {
            LineAgentPermission::firstOrCreate([
                'line_id' => $this->lineId,
                'agent_id' => $this->editingPermAgentId,
                'permission' => $perm,
            ]);
        }

        $this->editingPermAgentId = null;
        session()->flash('message', 'Permisos guardados.');
    }

    public function closePermissions(): void
    {
        $this->editingPermAgentId = null;
    }

    // ── Sales Stats ─────────────────────────────────────────────────────

    public function getSalesProperty()
    {
        return Sale::where('line_id', $this->lineId)
            ->orderByDesc('fecha')
            ->get();
    }

    public function getTotalSalesThisMonth(): float
    {
        return Sale::where('line_id', $this->lineId)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('monto_fichas');
    }

    public function getTotalSalesLast3Months(): array
    {
        $months = [];
        for ($i = 0; $i < 3; $i++) {
            $date = now()->subMonths($i);
            $months[] = [
                'mes' => $date->month,
                'anio' => $date->year,
                'nombre' => $date->monthName,
                'total' => Sale::where('line_id', $this->lineId)
                    ->whereMonth('fecha', $date->month)
                    ->whereYear('fecha', $date->year)
                    ->sum('monto_fichas'),
            ];
        }

        return $months;
    }

    public function getBestMonth(): ?array
    {
        $best = Sale::where('line_id', $this->lineId)
            ->get()
            ->groupBy(fn (Sale $sale) => $sale->fecha->format('Y-m'))
            ->map(fn ($sales) => [
                'mes' => $sales->first()->fecha->month,
                'anio' => $sales->first()->fecha->year,
                'total' => $sales->sum(fn (Sale $sale) => (float) $sale->monto_fichas),
            ])
            ->sortByDesc('total')
            ->first();

        if (! $best) {
            return null;
        }

        $monthName = now()->setDate($best['anio'], $best['mes'], 1)->monthName;

        return [
            'mes' => $best['mes'],
            'anio' => $best['anio'],
            'nombre' => $monthName,
            'total' => $best['total'],
        ];
    }

    public function getBestPlatformThisMonth(): ?array
    {
        $best = Sale::where('line_id', $this->lineId)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->orderByDesc('monto_fichas')
            ->with('platform')
            ->first();

        if (! $best) {
            return null;
        }

        return [
            'platform' => $best->platform->name,
            'total' => $best->monto_fichas,
        ];
    }

    public function getEncargadoEarningsThisMonth(): array
    {
        $lineAgents = LineAgent::where('line_id', $this->lineId)
            ->where('role', LineRoles::ENCARGADO)
            ->where('is_active', true)
            ->get();

        $totalSales = $this->getTotalSalesThisMonth();
        $earnings = [];

        foreach ($lineAgents as $la) {
            $porcentaje = $la->porcentaje_ganancia ?? 0;
            $ganancia = $totalSales * ($porcentaje / 100);
            $earnings[] = [
                'agent' => $la->agent,
                'porcentaje' => $porcentaje,
                'ganancia' => $ganancia,
            ];
        }

        return $earnings;
    }

    // ── Encargado Percentage ───────────────────────────────────────────────

    public function openPercentageModal(int $agentId): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        $la = LineAgent::where('line_id', $this->lineId)->where('agent_id', $agentId)->first();
        if ($la) {
            $this->percentageAgentId = $agentId;
            $this->percentageValue = $la->porcentaje_ganancia ?? '0';
            $this->showPercentageModal = true;
        }
    }

    public function closePercentageModal(): void
    {
        $this->showPercentageModal = false;
        $this->percentageAgentId = null;
        $this->percentageValue = '';
    }

    public function savePercentage(): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        if ($this->percentageAgentId) {
            LineAgent::where('line_id', $this->lineId)
                ->where('agent_id', $this->percentageAgentId)
                ->update(['porcentaje_ganancia' => (float) $this->percentageValue]);

            session()->flash('message', 'Porcentaje de ganancia actualizado.');
        }

        $this->closePercentageModal();
    }

    // ── Image URLs ───────────────────────────────────────────────────────────

    public function openImageModal(string $type): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        if ($type === 'portada') {
            $this->portadaUrl = $this->line->portada_url ?? '';
        } else {
            $this->perfilUrl = $this->line->perfil_url ?? '';
        }

        $this->dispatch('openImageUploadModal', ['type' => $type]);
    }

    public function saveImageUrl(string $type, string $url): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        if ($type === 'portada') {
            $this->line->update(['portada_url' => $url]);
        } else {
            $this->line->update(['perfil_url' => $url]);
        }

        $this->line->refresh();
        session()->flash('message', 'Imagen actualizada.');
    }

    // ── Delete Image ────────────────────────────────────────

    public function deleteImage(string $type): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        if ($type === 'portada') {
            $this->line->update(['portada_url' => null]);
        } elseif ($type === 'perfil') {
            $this->line->update(['perfil_url' => null]);
        }

        $this->line->refresh();
        session()->flash('message', 'Imagen eliminada.');
    }

    // ── Encargado selection ────────────────────────────────────────

    public function getAvailableAgentsProperty(): Collection
    {
        return Agent::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function getCurrentEncargadoProperty()
    {
        return $this->line->encargado;
    }

    public function assignEncargado($agentId): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        $this->line->update(['encargado_id' => $agentId ?: null]);
        $this->line->refresh();
        $this->selectedEncargadoId = $agentId;
        session()->flash('message', $agentId ? 'Encargado asignado.' : 'Encargado removido.');
    }

    // ── Line Permissions ──────────────────────────────────────────────────

    public function setAllLinePermissions(bool $grant): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        $this->linePermissionsList = $grant ? LineAgentPermission::allPermissions() : [];
        $this->line->update(['permissions' => $this->linePermissionsList]);
        $this->line->refresh();
        session()->flash('message', $grant ? 'Todos los permisos habilitados.' : 'Todos los permisos removidos.');
    }

    public function toggleLinePermission(string $permission): void
    {
        $this->checkLinePermission(Permissions::LINE_EDIT);

        if (! in_array($permission, LineAgentPermission::allPermissions(), true)) {
            return;
        }

        if (in_array($permission, $this->linePermissionsList)) {
            $this->linePermissionsList = array_filter($this->linePermissionsList, fn ($p) => $p !== $permission);
        } else {
            $this->linePermissionsList[] = $permission;
        }

        $this->line->update(['permissions' => array_values($this->linePermissionsList)]);
        $this->line->refresh();
        session()->flash('message', 'Permisos de línea actualizados.');
    }

    // ── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        $this->authorizeLineContext();
        $availableAgents = $this->availableAgents;
        $currentEncargado = $this->currentEncargado;
        $availablePlatforms = $this->availablePlatforms;

        return view('livewire.line-detail', compact(
            'availableAgents',
            'currentEncargado',
            'availablePlatforms'
        ))->layout('layouts.dashboard');
    }

    private function authorizeLineContext(): void
    {
        $this->line = Line::findOrFail($this->lineId);

        if ($this->isAdminMode()) {
            session(['active_line_id' => $this->lineId]);

            return;
        }

        // Validate session agent_id belongs to user
        $sessionAgentId = session('active_agent_id');
        if ($sessionAgentId && ! Agent::where('id', $sessionAgentId)->where('user_id', auth()->id())->exists()) {
            session()->forget(['active_agent_id', 'active_line_id']);
            $sessionAgentId = null;
        }

        $agentId = $sessionAgentId ?: auth()->user()?->agent?->id;
        $lineAgent = LineAgent::where('line_id', $this->lineId)
            ->where('agent_id', $agentId)
            ->where('is_active', true)
            ->first();

        if (! $lineAgent) {
            abort(403, 'No perteneces a esta linea.');
        }

        session([
            'active_agent_id' => $agentId,
            'active_line_id' => $this->lineId,
        ]);

        if (
            ! $lineAgent->hasPermission(Permissions::LINE_READ)
            && ! $lineAgent->hasPermission(Permissions::LINE_VIEW)
            && ! $lineAgent->hasPermission(Permissions::LINE_EDIT)
        ) {
            abort(403, 'Sin permiso para ver esta linea.');
        }

        view()->share('activeLine', $this->line);
        view()->share('currentLineAgent', $lineAgent);
    }
}
