<div class="page-container">
    <x-livewire.components.page-header title="REPORTES" subtitle="Estadísticas y análisis del negocio" />

    <div class="section-title">USUARIOS</div>
    <div class="stats-grid-4">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-label">TOTAL USUARIOS</div>
            <div class="stat-value">{{ number_format($userStats['total']) }}</div>
            <div class="stat-change {{ $userStats['growth'] >= 0 ? 'positive' : 'negative' }}">
                {{ $userStats['growth'] >= 0 ? '▲' : '▼' }} {{ abs($userStats['growth']) }}% vs mes anterior
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-label">NUEVOS ESTE MES</div>
            <div class="stat-value" style="color: var(--good);">{{ $userStats['thisMonth'] }}</div>
            <div class="stat-change">Mes anterior: {{ $userStats['lastMonth'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-label">ACTIVOS</div>
            <div class="stat-value" style="color: var(--orange);">{{ $userStats['active'] }}</div>
            <div class="stat-change">Bloqueados: {{ $userStats['blocked'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📆</div>
            <div class="stat-label">NUEVOS HOY</div>
            <div class="stat-value">{{ $userStats['today'] }}</div>
            <div class="stat-change">Esta semana: {{ $userStats['thisWeek'] }}</div>
        </div>
    </div>

    <div class="section-title">TICKETS & SOPORTE</div>
    <div class="stats-grid-4">
        <div class="stat-card">
            <div class="stat-icon">🎫</div>
            <div class="stat-label">TOTAL TICKETS</div>
            <div class="stat-value">{{ $ticketStats['total'] }}</div>
            <div class="stat-change">Esta semana: {{ $ticketStats['thisWeek'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-label">ABIERTOS</div>
            <div class="stat-value" style="color: var(--orange);">{{ $ticketStats['open'] }}</div>
            <div class="stat-change">Pendientes: {{ $ticketStats['pending'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-label">RESUELTOS</div>
            <div class="stat-value" style="color: var(--good);">{{ $ticketStats['closed'] }}</div>
            <div class="stat-change">Este mes: {{ $ticketStats['resolved'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚡</div>
            <div class="stat-label">TIEMPO PROMEDIO</div>
            <div class="stat-value">{{ $ticketStats['avgResponse'] }}</div>
            <div class="stat-change">Tiempo de respuesta</div>
        </div>
    </div>

    <div class="section-title">PROMOCIONES & CONTENIDO</div>
    <div class="stats-grid-4">
        <div class="stat-card">
            <div class="stat-icon">🎁</div>
            <div class="stat-label">TOTAL PROMOCIONES</div>
            <div class="stat-value">{{ $promotionStats['total'] }}</div>
            <div class="stat-change">Activas: {{ $promotionStats['active'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📢</div>
            <div class="stat-label">PRÓXIMAS</div>
            <div class="stat-value" style="color: var(--orange);">{{ $promotionStats['upcoming'] }}</div>
            <div class="stat-change">Finalizadas: {{ $promotionStats['ended'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-label">PUBLICACIONES</div>
            <div class="stat-value">{{ $contentStats['published'] }}</div>
            <div class="stat-change">Borradores: {{ $contentStats['draft'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📰</div>
            <div class="stat-label">POR TIPO</div>
            <div class="stat-value">{{ $contentStats['total'] }}</div>
            <div class="stat-change">Novedades: {{ $contentStats['novedades'] }} | Blog: {{ $contentStats['blog'] }}</div>
        </div>
    </div>

    <div class="section-title">AGENTES & EQUIPO</div>
    <div class="stats-grid-4">
        <div class="stat-card">
            <div class="stat-icon">👨‍💼</div>
            <div class="stat-label">TOTAL AGENTES</div>
            <div class="stat-value">{{ $agentStats['total'] }}</div>
            <div class="stat-change">Activos: {{ $agentStats['active'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-label">PADRES</div>
            <div class="stat-value">{{ $agentStats['parents'] }}</div>
            <div class="stat-change">Hijos: {{ $agentStats['children'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎁</div>
            <div class="stat-label">BONOS USADOS</div>
            <div class="stat-value" style="color: var(--good);">{{ $bonusStats['used'] }}</div>
            <div class="stat-change">Este mes: {{ $bonusStats['usedThisMonth'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎰</div>
            <div class="stat-label">NÚMEROS SORTEO</div>
            <div class="stat-value" style="color: var(--orange);">{{ $raffleStats['assignedNumbers'] }}</div>
            <div class="stat-change">De {{ $raffleStats['totalNumbers'] }} total</div>
        </div>
    </div>

    <div class="section-title">BONOS & SORTEOS</div>
    <div class="stats-grid-4">
        <div class="stat-card">
            <div class="stat-icon">🎁</div>
            <div class="stat-label">TOTAL ASIGNACIONES</div>
            <div class="stat-value">{{ $bonusStats['total'] }}</div>
            <div class="stat-change">Disponibles: {{ $bonusStats['available'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-label">USADOS</div>
            <div class="stat-value" style="color: var(--good);">{{ $bonusStats['used'] }}</div>
            <div class="stat-change">Expirados: {{ $bonusStats['expired'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎰</div>
            <div class="stat-label">SORTEOS ACTIVOS</div>
            <div class="stat-value" style="color: var(--orange);">{{ $raffleStats['active'] }}</div>
            <div class="stat-change">Próximos: {{ $raffleStats['upcoming'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-label">TERMINADOS</div>
            <div class="stat-value">{{ $raffleStats['ended'] }}</div>
            <div class="stat-change">Total sorteos: {{ $raffleStats['total'] }}</div>
        </div>
    </div>

    <div class="section-title">TOP USUARIOS RECIENTES</div>
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Tickets</th>
                    <th>Bonos</th>
                    <th>Registrado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topUsers as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="status-badge {{ $user->status }}">
                            {{ $user->status === 'active' ? 'Activo' : 'Bloqueado' }}
                        </span>
                    </td>
                    <td>{{ $user->tickets_count }}</td>
                    <td>{{ $user->bonuses_count }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    </div>

    <style>
        .section-title { font-size: 11px; color: var(--orange); font-weight: 700; letter-spacing: 0.12em; margin: 20px 0 12px 28px; }

        .stats-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; padding: 0 28px; margin-bottom: 12px; }

        .stat-card { background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line); border-radius: 12px; padding: 14px; }
        .stat-icon { font-size: 16px; margin-bottom: 6px; }
        .stat-label { font-size: 9px; color: var(--muted); font-weight: 700; letter-spacing: 0.1em; }
        .stat-value { font-family: var(--font-display); font-size: 26px; color: var(--white); margin: 2px 0; }
        .stat-change { font-size: 10px; color: var(--muted); }
        .stat-change.positive { color: var(--good); }
        .stat-change.negative { color: #ff4757; }

        .table-card { margin: 0 28px 28px; background: linear-gradient(180deg, #1c0d0a, #120909); border: 1px solid var(--line); border-radius: 14px; padding: 0; overflow: hidden; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { text-align: left; padding: 14px 16px; font-size: 10px; color: var(--muted); font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; border-bottom: 1px solid var(--line); }
        .data-table td { padding: 14px 16px; font-size: 12px; color: var(--white); border-bottom: 1px solid var(--line); }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover { background: rgba(255,255,255,0.02); }

        .status-badge { font-size: 10px; padding: 4px 10px; border-radius: 999px; font-weight: 600; }
        .status-badge.active { background: rgba(37,196,107,0.12); color: var(--good); }
        .status-badge.blocked { background: rgba(255,255,255,0.06); color: var(--muted-2); }

        @media (max-width: 1200px) {
            .stats-grid-4 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .stats-grid-4 { grid-template-columns: 1fr; }
            .section-title, .stats-grid-4, .table-card { padding-left: 16px; padding-right: 16px; }
            .table-card { overflow-x: auto; }
        }
    </style>
</div>