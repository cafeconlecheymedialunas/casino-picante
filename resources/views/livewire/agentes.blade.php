<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">AGENTES</h1>
            <p class="page-subtitle">Jerarquía padre → hijos · permisos granulares por sección</p>
        </div>
        <button wire:click="openCreateModal" class="btn-primary">
            <span>+</span> Crear agente
        </button>
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
                            <div class="parent-role">Líneas {{ implode(', ', $parent->lines ?? []) }} · {{ $parent->children->count() }} hijos</div>
                        </div>
                        <button class="btn-ghost" style="height: 30px; padding: 0 12px; font-size: 11px;">Gestionar</button>
                    </div>
                </div>
                @if($parent->children->count() > 0)
                <div class="children-wrapper">
                    <div class="children-line"></div>
                    @foreach($parent->children as $child)
                    <div class="child-card" wire:click="selectAgent({{ $child->id }})">
                        <div class="child-avatar">{{ strtoupper(substr($child->name, 0, 1)) }}</div>
                        <div class="child-info">
                            <div class="child-name">{{ $child->name }}</div>
                            <div class="child-role">Hijo · {{ implode(', ', $child->lines ?? []) }}</div>
                        </div>
                        <div class="child-lines">
                            @foreach($child->lines ?? [] as $line)
                            <span class="child-line-badge">{{ $line }}</span>
                            @endforeach
                        </div>
                        <div class="child-perms">{{ $child->permissions->count() }} permisos activos</div>
                        <button class="btn-ghost" style="height: 30px; padding: 0 12px; font-size: 11px;">Editar</button>
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
                <div class="perm-avatar">{{ strtoupper(substr($selectedAgent->name, 0, 1)) }}</div>
                <div>
                    <div class="perm-name">{{ $selectedAgent->name }}</div>
                    <div class="perm-role">Hijo de {{ $selectedAgent->parent->name ?? 'N/A' }} · Línea {{ implode(', ', $selectedAgent->lines ?? []) }}</div>
                </div>
            </div>
            <p class="perm-desc">Definí qué puede hacer este agente en cada sección. Los cambios se aplican al toque.</p>

            <div class="perm-section-label">Líneas asignadas</div>
            <div class="line-buttons">
                @foreach($selectedAgent->lines ?? [] as $line)
                <button class="line-btn">{{ $line }}</button>
                @endforeach
            </div>

            <div class="perm-section-label">Permisos por sección</div>
            <div class="perm-matrix">
                <div class="matrix-header">
                    <div style="text-align:left">Sección</div>
                    <div title="Sin acceso">∅</div>
                    <div title="Lectura">👁</div>
                    <div title="Crear">+</div>
                    <div title="Editar">✎</div>
                    <div title="Eliminar">✕</div>
                </div>
                @php
                $sections = ['Blog', 'Novedades', 'Promociones', 'Carrusel', 'Tickets', 'Usuarios'];
                $permissions = ['none', 'read', 'create', 'edit', 'delete'];
                @endphp
                @foreach($sections as $section)
                <div class="matrix-row">
                    <div>{{ $section }}</div>
                    @foreach($permissions as $perm)
                    <div class="matrix-cell">
                        <div class="matrix-check {{ $perm === 'edit' ? 'selected' : '' }}">●</div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>

            <div class="perm-actions">
                <button class="btn-ghost perm-cancel">Cancelar</button>
                <button class="btn-primary perm-save">Guardar permisos</button>
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
            <form class="modal-form">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" placeholder="Nombre del agente">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" placeholder="correo@ejemplo.com">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" placeholder="+54 9 11 9999 9999">
                </div>
                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn-ghost">Cancelar</button>
                    <button type="submit" class="btn-primary">{{ $editingAgent ? 'Guardar' : 'Crear' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            padding: 0 28px;
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
    </style>
</div>