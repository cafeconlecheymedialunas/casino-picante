<div>
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">OVERVIEW</h1>
            <p class="page-subtitle">Resumen general · {{ now()->format('d M Y') }}</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-label">Usuarios totales</div>
            <div class="stat-value">{{ number_format($metrics['totalUsers']) }}</div>
            <div class="stat-change {{ $metrics['usersGrowth'] >= 0 ? 'positive' : 'negative' }}">
                {{ $metrics['usersGrowth'] >= 0 ? '▲' : '▼' }} {{ abs($metrics['usersGrowth']) }}% vs yesterday
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-label">Registros hoy</div>
            <div class="stat-value">{{ $metrics['todayUsers'] }}</div>
            <div class="stat-change">▲ +{{ $metrics['todayUsers'] - ($metrics['weekUsers'] / 7) }} vs avg</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎁</div>
            <div class="stat-label">Promos activas</div>
            <div class="stat-value">{{ $metrics['activePromos'] }}</div>
            <div class="stat-change neutral">{{ $metrics['totalPromos'] }} totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎫</div>
            <div class="stat-label">Tickets abiertos</div>
            <div class="stat-value">{{ $metrics['openTickets'] }}</div>
            <div class="stat-change neutral">Tickets sin atender</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">ÚLTIMOS REGISTROS</h3>
                <a href="{{ route('users.index') }}" class="card-link">Ver todos →</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Línea</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td><span class="line-badge">L1</span></td>
                        <td>
                            @if($user->email_verified_at)
                            <span class="status-badge active">VERIFICADO</span>
                            @else
                            <span class="status-badge pending">PENDIENTE</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                    @if($recentUsers->isEmpty())
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--muted);">Sin registros recientes</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div>
            <div class="card" style="margin-bottom: 14px;">
                <div class="card-header">
                    <h3 class="card-title">MÉTRICAS</h3>
                </div>
                <div class="mini-stats">
                    <div class="mini-stat">
                        <div class="mini-stat-label">Depósitos hoy</div>
                        <div class="mini-stat-value">${{ number_format($metrics['depositsToday']) }}</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-label">Retiros hoy</div>
                        <div class="mini-stat-value">${{ number_format($metrics['withdrawalsToday']) }}</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-label">Jugadas</div>
                        <div class="mini-stat-value">{{ number_format($metrics['playsToday']) }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ACCIONES RÁPIDAS</h3>
                </div>
                <div class="quick-actions">
                    <div class="quick-action">
                        <span>Promo activa</span>
                        <strong style="color: var(--good);">● {{ $metrics['activePromos'] }}</strong>
                    </div>
                    <div class="quick-action">
                        <span>Líneas activas</span>
                        <strong style="color: var(--good);">● 6</strong>
                    </div>
                    <div class="quick-action">
                        <span>Usuarios online</span>
                        <strong>{{ number_format($metrics['onlineUsers']) }}</strong>
                    </div>
                    <div class="quick-action">
                        <span>Tickets sin atender</span>
                        <strong style="color: var(--warn);">{{ $metrics['openTickets'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            padding: 0 28px 28px;
        }
        @media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr; } }
        .stat-card {
            padding: 18px;
            background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
        }
        .stat-icon { font-size: 24px; margin-bottom: 8px; }
        .stat-label { font-size: 11px; color: var(--muted); letter-spacing: 0.08em; font-weight: 700; text-transform: uppercase; }
        .stat-value { font-family: var(--font-display); font-size: 38px; margin-top: 8px; }
        .stat-change { font-size: 11px; color: var(--good); margin-top: 6px; }
        .stat-change.positive { color: var(--good); }
        .stat-change.negative { color: #ff4757; }
        .stat-change.neutral { color: var(--muted); }
        .grid-2 {
            padding: 0 28px 28px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 14px;
        }
        @media (max-width: 1024px) { .grid-2 { grid-template-columns: 1fr; } }
        .card {
            padding: 22px;
            background: linear-gradient(180deg, #170b0b 0%, #0f0707 100%);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
        }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .card-title { font-family: var(--font-display); font-size: 20px; letter-spacing: 0.02em; margin: 0; }
        .card-link { font-size: 11px; color: var(--orange); font-weight: 700; text-decoration: none; }
        .card-link:hover { text-decoration: underline; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { text-align: left; padding: 12px; font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); border-bottom: 1px solid var(--line); }
        .data-table td { padding: 12px; border-bottom: 1px solid var(--line); font-size: 13px; }
        .data-table tr:hover td { background: rgba(255,255,255,0.02); }
        .status-badge { padding: 4px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; }
        .status-badge.active { background: rgba(37,196,107,0.15); color: var(--good); }
        .status-badge.pending { background: rgba(255,179,71,0.15); color: var(--warn); }
        .line-badge { padding: 2px 6px; border-radius: 4px; background: rgba(255,106,26,0.12); color: var(--orange); font-size: 10px; font-weight: 700; }
        .mini-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .mini-stat { padding: 14px; border-radius: 10px; background: rgba(255,255,255,0.03); }
        .mini-stat-label { font-size: 10px; color: var(--muted); letter-spacing: 0.08em; font-weight: 700; text-transform: uppercase; }
        .mini-stat-value { font-family: var(--font-display); font-size: 24px; margin-top: 4px; }
        .quick-actions { display: grid; gap: 8px; }
        .quick-action { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-radius: 8px; background: rgba(255,255,255,0.03); }
        .quick-action span { color: var(--muted); font-size: 13px; }
        .quick-action strong { font-size: 13px; }
    </style>
</div>