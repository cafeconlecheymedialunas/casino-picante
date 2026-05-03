<div class="page-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">AGENTES</h1>
            <p class="page-subtitle">Jerarquía padre → hijos · permisos granulares por sección</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <input type="text" placeholder="Buscar agentes..." wire:model="search" class="search-input">
            <button wire:click="openCreateModal" class="btn-primary">
                <span>+</span> Crear agente
            </button>
        </div>
    </div>

    <div class="content-grid">
        <div class="tree-section">
            <h3 class="section-title">ÁRBOL DE AGENTES</h3>

            @php
            $admin = (object)['name' => 'Sofía Castro', 'role' => 'admin', 'email' => 'sofia@redpicantes.com'];
            $parents = App\Models\Agent::where('role', 'parent')->with('children')->get();
            @endphp

            <div class="admin-node">
                <div class="admin-avatar">★</div>
                <div class="admin-info">
                    <div class="admin-name">{{ $admin->name }}</div>
                    <div class="admin-role">Admin general · acceso total</div>
                </div>
                <span class="admin-chip">ROOT</span>
            </div>

            @forelse($parents as $parent)
            <div class="parent-wrapper">
                <div class="parent-line"></div>
                <div class="parent-node">
                    <div class="parent-branch"></div>
                    <div class="parent-card">
                        <div class="parent-avatar">{{ strtoupper(substr($parent->name, 0, 1)) }}</div>
                        <div class="parent-info">
                            <div class="parent-name">
                                {{ $parent->name }}
                                <span class="parent-badge">PADRE</span>
                            </div>
                            <div class="parent-role">Líneas {{ $parent->activeLines->pluck('name')->implode(', ') ?: '—' }} · {{ $parent->children->count() }} hijos</div>
                        </div>
                        <button class="btn-ghost" style="height: 30px; padding: 0 12px; font-size: 11px;" wire:click="selectAgent({{ $parent->id }})">Gestionar</button>
                    </div>
                </div>
                @if($parent->children->count() > 0)
                <div class="children-wrapper">
                    <div class="children-line"></div>
                    @foreach($parent->children as $child)
                    <div class="child-card">
                        <div class="child-avatar" wire:click="selectAgent({{ $child->id }})" style="cursor:pointer">{{ strtoupper(substr($child->name, 0, 1)) }}</div>
                        <div class="child-info" wire:click="selectAgent({{ $child->id }})" style="cursor:pointer">
                            <div class="child-name">{{ $child->name }}</div>
                            <div class="child-role">Hijo · {{ $child->activeLines->pluck('name')->implode(', ') ?: '—' }}</div>
                        </div>
                        <div class="child-lines">
                            @foreach($child->activeLines as $line)
                            <span class="child-line-badge">{{ $line->name }}</span>
                            @endforeach
                        </div>
                        <div class="child-perms">{{ $child->permissions->count() }} permisos activos</div>
                        <button class="btn-ghost" style="height: 30px; padding: 0 12px; font-size: 11px;" wire:click="openEditModal({{ $child->id }})">Editar</button>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="empty-state">
                <p>No hay agentes padre creados</p>
            </div>
            @endforelse
        </div>

        <div class="perm-panel">
            @if($selectedAgent)
            <div class="perm-header">
                <button wire:click="selectedAgent = null" class="perm-close" title="Cerrar">✕</button>
                <div class="perm-avatar">{{ strtoupper(substr($selectedAgent->name, 0, 1)) }}</div>
                <div>
                    <div class="perm-name">{{ $selectedAgent->name }}</div>
                    <div class="perm-role">Hijo de {{ $selectedAgent->parent->name ?? 'N/A' }} · Línea {{ $selectedAgent->activeLines->pluck('name')->implode(', ') ?: '—' }}</div>
                </div>
            </div>
            <p class="perm-desc">Definí qué puede hacer este agente en cada sección. Los cambios se aplican al toque.</p>

            <div class="perm-section-label">Líneas asignadas</div>
            <div class="line-buttons">
                @forelse($selectedAgent->activeLines as $line)
                <button class="line-btn">{{ $line->name }}</button>
                @empty
                <span style="color:var(--muted);font-size:12px;">Sin líneas asignadas</span>
                @endforelse
            </div>

            @if($selectedAgent->role_id)
            <div class="perm-section-label">ROL ASIGNADO (Global)</div>
            <div class="role-selector">
                <div class="current-role">
                    <span class="role-badge">{{ $selectedAgent->roleModel->name ?? 'Rol eliminado' }}</span>
                    <button wire:click="removeRole({{ $selectedAgent->id }})" class="btn-remove-role" title="Quitar rol">✕</button>
                </div>
            </div>
            @if($selectedAgent->roleModel && is_array($selectedAgent->roleModel->permissions))
            <div class="role-permissions">
                @foreach($selectedAgent->roleModel->permissions as $section => $level)
                @if($level !== 'none')
                <div class="perm-item">
                    <span class="perm-section-name">{{ $section }}</span>
                    <span class="perm-level">{{ $level }}</span>
                </div>
                @endif
                @endforeach
            </div>
            @endif
            @else

            <div class="perm-section-label">PERMISOS PERSONALIZADOS</div>
            <p style="font-size:11px;color:var(--muted);margin-bottom:12px;">
                El padre puede configurar exactamente qué puede hacer este hijo en cada sección
            </p>

            <div class="custom-perms">
                @php
                $sections = [
                    'blog' => 'Blog',
                    'novedades' => 'Novedades',
                    'promociones' => 'Promociones',
                    'carrusel' => 'Carrusel',
                    'tickets' => 'Tickets',
                    'usuarios' => 'Usuarios',
                    'metricas' => 'Métricas',
                    'contactos' => 'Enlaces de contacto',
                ];
                $levels = ['none' => '∅', 'read' => '👁', 'create' => '+', 'edit' => '✎', 'delete' => '✕'];
                $levelLabels = ['none' => 'Sin acceso', 'read' => 'Solo lectura', 'create' => 'Crear', 'edit' => 'Editar', 'delete' => 'Eliminar'];
                @endphp

                @foreach($sections as $sectionKey => $sectionLabel)
                <div class="perm-section-row">
                    <div class="perm-section-title">{{ $sectionLabel }}</div>
                    <div class="perm-levels">
                        @foreach($levels as $levelKey => $levelIcon)
                        <button 
                            class="perm-level-btn {{ ($permSections[$sectionKey] ?? 'none') === $levelKey ? 'active' : '' }}"
                            wire:click="togglePerm('{{ $sectionKey }}', '{{ $levelKey }}')"
                            title="{{ $levelLabels[$levelKey] }}">
                            {{ $levelIcon }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="perm-actions">
                <button class="btn-ghost perm-cancel" wire:click="selectedAgent = null">Cancelar</button>
                <button class="btn-primary perm-save" wire:click="savePermissions">Guardar permisos</button>
            </div>

            <div style="margin-top:16px;padding:12px;background:rgba(255,106,26,0.1);border-radius:8px;">
                <div style="font-size:11px;color:var(--orange);font-weight:700;margin-bottom:8px;">RESUMEN DE PERMISOS</div>
                @php $activePerms = 0; @endphp
                @foreach($permSections as $section => $level)
                    @if($level !== 'none')
                    @php $activePerms++; @endphp
                    <div style="font-size:11px;color:var(--white);margin-bottom:4px;">
                        <span style="color:var(--orange);">{{ $section }}:</span> {{ $level }}
                    </div>
                    @endif
                @endforeach
                @if($activePerms === 0)
                <div style="font-size:11px;color:var(--muted);">Sin permisos activos</div>
                @else
                <div style="font-size:11px;color:var(--good);margin-top:8px;">{{ $activePerms }} permisos activos</div>
                @endif
            </div>
            @endif

            <div style="margin-top:20px;text-align:center;">
                <button class="btn-ghost" style="color:#ff4757;border-color:#ff4757;" wire:click="deleteAgent({{ $selectedAgent->id }})">Eliminar agente</button>
            </div>
            @else
            <div class="perm-empty">
                <p>Selecciona un agente para ver y editar sus permisos</p>
            </div>
            @endif
        </div>
    </div>

    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h3>{{ $editingAgent ? 'EDITAR AGENTE' : 'NUEVO AGENTE' }}</h3>
                <button class="modal-close" wire:click="closeModal">✕</button>
            </div>
            <form class="modal-form" wire:submit.prevent="saveAgent">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" placeholder="Nombre del agente" wire:model="name" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" placeholder="correo@ejemplo.com" wire:model="email" required>
                </div>
                <div class="form-group">
                    <label>Contraseña {{ $editingAgent ? '(dejar vacío para mantener)' : '*' }}</label>
                    <input type="password" placeholder="••••••••" wire:model="password" {{ $editingAgent ? '' : 'required' }}>
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" placeholder="+54 9 11 9999 9999" wire:model="phone">
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select wire:model="role" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="parent"> Padre</option>
                        <option value="child">Hijo</option>
                    </select>
                </div>
                @if($role === 'child')
                <div class="form-group">
                    <label>Agente padre</label>
                    <select wire:model="parent_id" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="">Seleccionar padre...</option>
                        @foreach($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="form-group">
                    <label>Rol (opcional)</label>
                    <select wire:model="role_id" style="width:100%;background:linear-gradient(180deg,#1c0d0a,#120909);border:1px solid var(--line-warm);border-radius:10px;padding:12px 16px;color:var(--white);font-size:14px;">
                        <option value="">Sin rol asignado</option>
                        @forelse($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @empty
                        <option value="" disabled>No hay roles creados</option>
                        @endforelse
                    </select>
                </div>
                <div class="form-group">
                    <label>Líneas asignadas</label>
                    <p style="font-size:12px;color:var(--muted);margin-top:6px;">La asignación de líneas se gestiona en <a href="{{ route('lineas') }}" style="color:var(--orange);">Líneas & Redes</a>.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingAgent ? 'Guardar' : 'Crear' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if(session()->has('message'))
    <div style="position:fixed;top:20px;right:20px;background:var(--good);color:#000;padding:12px 20px;border-radius:8px;font-weight:700;z-index:2000;">
        {{ session('message') }}
    </div>
    @endif

    </div>

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--black);
            margin: -24px -28px 24px -28px;
            padding: 24px 28px 16px;
            border-bottom: 1px solid var(--line);
        }
        .page-title {
            font-family: var(--font-display);
            font-size: 36px;
            color: var(--white);
            margin: 0;
        }
        .page-subtitle {
            color: var(--muted);
            font-size: 12px;
            margin-top: 2px;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 20px;
            padding: 0 28px 28px;
        }
        @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }
        
        .section-title {
            font-family: var(--font-display);
            font-size: 22px;
            margin: 0 0 14px;
        }

        .admin-node {
            background: radial-gradient(120% 80% at 0% 0%, rgba(255,106,26,0.15) 0%, transparent 60%), linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 12px;
        }
        .admin-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--orange), var(--amber));
            color: #190702; font-weight: 800; display: flex;
            align-items: center; justify-content: center; font-size: 16px;
        }
        .admin-info { flex: 1; }
        .admin-name { font-weight: 700; }
        .admin-role { font-size: 11px; color: var(--muted); }
        .admin-chip {
            padding: 4px 10px; border-radius: 6px;
            background: var(--orange); color: #190702;
            font-size: 10px; font-weight: 700; letter-spacing: 0.08em;
        }

        .parent-wrapper { position: relative; padding-left: 24px; margin-bottom: 10px; }
        .parent-line { position: absolute; left: 8px; top: 0; bottom: 28px; width: 2px; background: rgba(255,106,26,0.25); }
        .parent-node { position: relative; margin-bottom: 10px; }
        .parent-branch { position: absolute; left: -16px; top: 22px; width: 14px; height: 2px; background: rgba(255,106,26,0.25); }
        .parent-card {
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid rgba(255,106,26,0.3); border-radius: 14px;
            padding: 14px; display: flex; gap: 12px; align-items: center;
        }
        .parent-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,138,61,0.18); border: 1px solid rgba(255,138,61,0.4);
            color: var(--orange-2); font-weight: 800;
            display: flex; align-items: center; justify-content: center;
        }
        .parent-info { flex: 1; }
        .parent-name { font-weight: 700; font-size: 13px; }
        .parent-role { font-size: 11px; color: var(--muted); }
        .parent-badge { font-size: 10px; margin-left: 6px; padding: 2px 6px; border-radius: 4px; background: rgba(255,106,26,0.15); color: var(--orange); font-weight: 700; }

        .children-wrapper { display: grid; gap: 8px; padding-left: 24px; position: relative; }
        .children-line { position: absolute; left: 0; top: -8px; bottom: 18px; width: 2px; background: rgba(255,255,255,0.08); }
        .child-card {
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm); border-radius: 14px;
            padding: 14px; display: flex; gap: 12px; align-items: center;
            cursor: pointer; transition: all 0.2s;
        }
        .child-card:hover { border-color: var(--orange); }
        .child-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,106,26,0.12); border: 1px solid rgba(255,106,26,0.3);
            color: var(--orange); font-weight: 800;
            display: flex; align-items: center; justify-content: center;
        }
        .child-info { flex: 1; }
        .child-name { font-weight: 700; font-size: 13px; }
        .child-role { font-size: 11px; color: var(--muted); }
        .child-lines { display: flex; gap: 4px; }
        .child-line-badge { padding: 3px 7px; border-radius: 6px; background: rgba(255,106,26,0.12); color: var(--orange); font-size: 10px; font-weight: 700; }
        .child-perms { font-size: 11px; color: var(--muted); min-width: 140px; text-align: right; }

        .perm-panel {
            background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm); border-radius: 14px;
            padding: 20px; height: fit-content; position: sticky; top: 20px;
        }
        .perm-empty { text-align: center; color: var(--muted); padding: 40px; }
        .perm-header { display: flex; align-items: center; gap: 10; margin-bottom: 4px; }
        .perm-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,106,26,0.12); border: 1px solid rgba(255,106,26,0.3);
            color: var(--orange); font-weight: 800;
            display: flex; align-items: center; justify-content: center;
        }
        .perm-name { font-weight: 700; }
        .perm-role { font-size: 11px; color: var(--muted); }
        .perm-desc { font-size: 11px; color: var(--muted); margin: 12px 0; }
        .perm-section-label { font-size: 10px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .line-buttons { display: flex; gap: 6px; margin-bottom: 16px; }
        .line-btn {
            flex: 1; height: 32px; border-radius: 8px;
            font-size: 11px; font-weight: 700;
            background: rgba(255,106,26,0.18); border: 1px solid var(--orange);
            color: var(--orange); cursor: pointer;
        }
        .perm-matrix { border: 1px solid var(--line); border-radius: 10px; overflow: hidden; }
        .matrix-header {
            display: grid; grid-template-columns: 1fr repeat(5, 36px);
            font-size: 9px; color: var(--muted); padding: 8px 12px;
            background: rgba(255,255,255,0.02); font-weight: 700;
            letter-spacing: 0.06em; text-transform: uppercase; text-align: center;
        }
        .matrix-row {
            display: grid; grid-template-columns: 1fr repeat(5, 36px);
            align-items: center; padding: 8px 12px; border-top: 1px solid var(--line); font-size: 12px;
        }
        .matrix-cell { display: flex; justify-content: center; }
        .matrix-check {
            width: 22px; height: 22px; border-radius: 6px;
            background: rgba(255,106,26,0.2); border: 1px solid var(--line);
            color: var(--orange); font-size: 10px; font-weight: 800;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
        }
        .matrix-check.selected { background: var(--orange); color: #190702; border: none; }
        .perm-actions { display: flex; gap: 8px; margin-top: 14px; }
        .perm-cancel { flex: 1; height: 36px; font-size: 12px; font-weight: 700; }
        .perm-save { flex: 2; height: 36px; font-size: 12px; }

        .empty-state { text-align: center; color: var(--muted); padding: 20px; }

        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.8); display: flex;
            align-items: center; justify-content: center; z-index: 1000; padding: 20px;
        }
        .modal-content {
            background: linear-gradient(180deg, #1a0d0d 0%, #120909 100%);
            border: 1px solid var(--line); border-radius: 20px;
            width: 100%; max-width: 480px;
        }
        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 24px; border-bottom: 1px solid var(--line);
        }
        .modal-header h3 { font-family: var(--font-display); font-size: 22px; margin: 0; }
        .modal-close { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; }
        .modal-form { padding: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .form-group input {
            width: 100%; background: linear-gradient(180deg, #1c0d0a, #120909);
            border: 1px solid var(--line-warm); border-radius: 10px;
            padding: 12px 16px; color: var(--white); font-size: 14px;
        }
        .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; }
        .search-input { width: 200px; padding: 10px 16px; border-radius: 999px; background: rgba(255,255,255,0.04); border: 1px solid var(--line-2); font-size: 12px; color: var(--muted); }
        .search-input:focus { outline: none; border-color: var(--orange); color: var(--white); }
        .line-btn.selected { background: var(--orange); color: #190702; }
        .line-btn:hover { background: rgba(255,106,26,0.3); }
        .perm-close { position: absolute; top: 16px; right: 16px; background: none; border: none; color: var(--muted); font-size: 18px; cursor: pointer; }
        .perm-close:hover { color: var(--orange); }
        .perm-header { position: relative; }
        
        .role-selector { margin-top: 12px; }
        .current-role { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
        .role-badge { background: linear-gradient(135deg, var(--orange), var(--amber)); color: #190702; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; }
        .btn-remove-role { width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.1); border: none; color: var(--muted); cursor: pointer; font-size: 10px; }
        .btn-remove-role:hover { background: #ff4757; color: #fff; }
        
        .role-permissions { display: grid; gap: 6px; margin-top: 12px; }
        .perm-item { display: flex; justify-content: space-between; padding: 8px 12px; background: rgba(255,255,255,0.04); border-radius: 6px; font-size: 12px; }
        .perm-section-name { color: var(--white); }
        .perm-level { color: var(--orange); font-weight: 600; text-transform: capitalize; }
        
        .custom-perms { border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
        .perm-section-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; border-bottom: 1px solid var(--line); }
        .perm-section-row:last-child { border-bottom: none; }
        .perm-section-title { font-size: 12px; color: var(--white); }
        .perm-levels { display: flex; gap: 4px; }
        .perm-level-btn { width: 28px; height: 28px; border-radius: 6px; background: rgba(255,255,255,0.04); border: 1px solid var(--line); cursor: pointer; font-size: 12px; transition: all 0.2s; }
        .perm-level-btn:hover { background: rgba(255,106,26,0.2); }
        .perm-level-btn.active { background: var(--orange); color: #190702; border: none; }
        
        .perm-actions { display: flex; gap: 8px; margin-top: 16px; }
        .perm-actions .btn-ghost { flex: 1; }
        .perm-actions .btn-primary { flex: 2; }
    </style>
</div>