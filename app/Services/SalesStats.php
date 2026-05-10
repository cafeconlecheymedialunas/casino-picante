<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Line;
use App\Models\Platform;
use App\Models\Sale;
use App\Models\User;
use App\Support\LineRoles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesStats
{
    public static function lineStats(Line $line, ?int $month = null, ?int $year = null): array
    {
        $month ??= now()->month;
        $year ??= now()->year;

        [$monthFn, $yearFn] = self::dateFunctions();

        $bestMonth = Sale::where('line_id', $line->id)
            ->selectRaw("{$yearFn} as anio, {$monthFn} as mes, SUM(monto_fichas) as total")
            ->groupByRaw("{$yearFn}, {$monthFn}")
            ->orderByDesc('total')
            ->first();

        $bestPlatform = Sale::where('line_id', $line->id)
            ->whereRaw("{$monthFn} = ?", [$month])
            ->whereRaw("{$yearFn} = ?", [$year])
            ->selectRaw('platform_id, SUM(monto_fichas) as total')
            ->groupBy('platform_id')
            ->orderByDesc('total')
            ->first();

        if ($bestPlatform) {
            $bestPlatform->setRelation('platform', Platform::find($bestPlatform->platform_id));
        }

        $lastMonths = Sale::where('line_id', $line->id)
            ->selectRaw("{$yearFn} as anio, {$monthFn} as mes, SUM(monto_fichas) as total")
            ->groupByRaw("{$yearFn}, {$monthFn}")
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->limit(3)
            ->get();

        $monthTotal = (float) Sale::where('line_id', $line->id)
            ->whereRaw("{$monthFn} = ?", [$month])
            ->whereRaw("{$yearFn} = ?", [$year])
            ->sum('monto_fichas');

        $lineAgents = $line->relationLoaded('lineAgents')
            ? $line->lineAgents
            : $line->lineAgents()->with('agent')->get();

        $earnings = $lineAgents
            ->where('role', LineRoles::ENCARGADO)
            ->map(fn ($lineAgent) => [
                'name' => trim(($lineAgent->agent?->name ?? 'Encargado').' '.($lineAgent->agent?->apellido ?? '')),
                'porcentaje' => (float) ($lineAgent->porcentaje_ganancia ?? 0),
                'ganancia' => $monthTotal * ((float) ($lineAgent->porcentaje_ganancia ?? 0) / 100),
            ])
            ->values();

        return compact('bestMonth', 'bestPlatform', 'lastMonths', 'earnings', 'monthTotal');
    }

    public static function bestSellingLineOfMonth(?int $month = null, ?int $year = null): ?array
    {
        $month ??= now()->month;
        $year ??= now()->year;

        [$monthFn, $yearFn] = self::dateFunctions();

        $sale = Sale::query()
            ->join('lines', 'lines.id', '=', 'sales.line_id')
            ->where('lines.status', 'active')
            ->whereRaw("{$monthFn} = ?", [$month])
            ->whereRaw("{$yearFn} = ?", [$year])
            ->selectRaw('sales.line_id, SUM(sales.monto_fichas) as total')
            ->groupBy('sales.line_id')
            ->orderByDesc('total')
            ->first();

        if (! $sale) {
            return null;
        }

        $line = Line::find($sale->line_id);

        return $line ? [
            'id' => $line->id,
            'name' => $line->name,
            'icon' => $line->icon ?: '●',
            'best_sales' => (float) $sale->total,
        ] : null;
    }

    public static function globalMonthStats(Collection $lines, ?int $month = null, ?int $year = null): array
    {
        $month ??= now()->month;
        $year ??= now()->year;
        $lineIds = $lines->pluck('id')->all();

        [$monthFn, $yearFn] = self::dateFunctions();

        $query = Sale::whereIn('line_id', $lineIds)
            ->whereRaw("{$monthFn} = ?", [$month])
            ->whereRaw("{$yearFn} = ?", [$year]);

        $total = (float) (clone $query)->sum('monto_fichas');
        $records = (clone $query)->count();
        $lineCount = (clone $query)->distinct('line_id')->count('line_id');
        $platformCount = (clone $query)->distinct('platform_id')->count('platform_id');

        $bestLine = (clone $query)
            ->selectRaw('line_id, SUM(monto_fichas) as total')
            ->groupBy('line_id')
            ->orderByDesc('total')
            ->first();

        $bestPlatform = (clone $query)
            ->selectRaw('platform_id, SUM(monto_fichas) as total')
            ->groupBy('platform_id')
            ->orderByDesc('total')
            ->first();

        return [
            'total' => $total,
            'records' => $records,
            'lineCount' => $lineCount,
            'platformCount' => $platformCount,
            'bestLine' => $bestLine ? [
                'line' => Line::find($bestLine->line_id),
                'total' => (float) $bestLine->total,
            ] : null,
            'bestPlatform' => $bestPlatform ? [
                'platform' => Platform::find($bestPlatform->platform_id),
                'total' => (float) $bestPlatform->total,
            ] : null,
        ];
    }

    public static function globalDateRangeStats(Collection $lines, ?string $dateInicio = null, ?string $dateFin = null): array
    {
        $lineIds = $lines->pluck('id')->all();

        $query = Sale::whereIn('line_id', $lineIds)
            ->when($dateInicio && $dateFin, function ($q) use ($dateInicio, $dateFin) {
                $q->whereDate('fecha_inicio', '>=', $dateInicio)
                    ->whereDate('fecha_fin', '<=', $dateFin);
            });

        $total = (float) (clone $query)->sum('monto_fichas');
        $records = (clone $query)->count();
        $lineCount = (clone $query)->distinct('line_id')->count('line_id');
        $platformCount = (clone $query)->distinct('platform_id')->count('platform_id');

        $bestLine = (clone $query)
            ->selectRaw('line_id, SUM(monto_fichas) as total')
            ->groupBy('line_id')
            ->orderByDesc('total')
            ->first();

        $bestPlatform = (clone $query)
            ->selectRaw('platform_id, SUM(monto_fichas) as total')
            ->groupBy('platform_id')
            ->orderByDesc('total')
            ->first();

        return [
            'total' => $total,
            'records' => $records,
            'lineCount' => $lineCount,
            'platformCount' => $platformCount,
            'bestLine' => $bestLine ? [
                'line' => Line::find($bestLine->line_id),
                'total' => (float) $bestLine->total,
            ] : null,
            'bestPlatform' => $bestPlatform ? [
                'platform' => Platform::find($bestPlatform->platform_id),
                'total' => (float) $bestPlatform->total,
            ] : null,
        ];
    }

    private static function dateFunctions(): array
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return ["CAST(strftime('%m', fecha_inicio) AS INTEGER)", "CAST(strftime('%Y', fecha_inicio) AS INTEGER)"];
        }

        return ['MONTH(fecha_inicio)', 'YEAR(fecha_inicio)'];
    }

    private static function dateFn(): string
    {
        return DB::connection()->getDriverName() === 'sqlite' ? 'date(fecha_inicio)' : 'DATE(fecha_inicio)';
    }

    public static function globalTotalSales(?Collection $lines = null): float
    {
        $lineIds = $lines ? $lines->pluck('id')->all() : Line::where('status', 'active')->pluck('id')->all();
        if (empty($lineIds)) {
            return 0;
        }

        $month = now()->month;
        $year = now()->year;
        [$monthFn, $yearFn] = self::dateFunctions();

        return (float) Sale::whereIn('line_id', $lineIds)
            ->whereRaw("{$monthFn} = ?", [$month])
            ->whereRaw("{$yearFn} = ?", [$year])
            ->sum('monto_fichas');
    }

    public static function globalTopPlatform(?Collection $lines = null): ?array
    {
        $lineIds = $lines ? $lines->pluck('id')->all() : Line::where('status', 'active')->pluck('id')->all();
        if (empty($lineIds)) {
            return null;
        }

        $month = now()->month;
        $year = now()->year;
        [$monthFn, $yearFn] = self::dateFunctions();

        $best = Sale::whereIn('line_id', $lineIds)
            ->whereRaw("{$monthFn} = ?", [$month])
            ->whereRaw("{$yearFn} = ?", [$year])
            ->selectRaw('platform_id, SUM(monto_fichas) as total')
            ->groupBy('platform_id')
            ->orderByDesc('total')
            ->first();

        if (! $best) {
            return null;
        }
        $platform = Platform::find($best->platform_id);

        return $platform ? ['name' => $platform->name, 'total' => (float) $best->total] : null;
    }

    public static function globalMonthlyGrowth(?Collection $lines = null): array
    {
        $lineIds = $lines ? $lines->pluck('id')->all() : Line::where('status', 'active')->pluck('id')->all();
        if (empty($lineIds)) {
            return ['current' => 0, 'previous' => 0, 'percent' => 0, 'direction' => 'neutral'];
        }

        $cm = now()->month;
        $cy = now()->year;
        $pm = now()->subMonth()->month;
        $py = now()->subMonth()->year;
        [$monthFn, $yearFn] = self::dateFunctions();

        $current = (float) Sale::whereIn('line_id', $lineIds)->whereRaw("{$monthFn} = ?", [$cm])->whereRaw("{$yearFn} = ?", [$cy])->sum('monto_fichas');
        $previous = (float) Sale::whereIn('line_id', $lineIds)->whereRaw("{$monthFn} = ?", [$pm])->whereRaw("{$yearFn} = ?", [$py])->sum('monto_fichas');

        $percent = $previous > 0 ? round(($current - $previous) / $previous * 100) : ($current > 0 ? 100 : 0);

        return ['current' => $current, 'previous' => $previous, 'percent' => abs($percent), 'direction' => $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'neutral')];
    }

    public static function globalTopBuyer(?Collection $lines = null): ?array
    {
        $lineIds = $lines ? $lines->pluck('id')->all() : Line::where('status', 'active')->pluck('id')->all();
        if (empty($lineIds)) {
            return null;
        }

        $month = now()->month;
        $year = now()->year;
        [$monthFn, $yearFn] = self::dateFunctions();

        $top = Sale::whereIn('line_id', $lineIds)->whereRaw("{$monthFn} = ?", [$month])->whereRaw("{$yearFn} = ?", [$year])
            ->selectRaw('client_id, SUM(monto_fichas) as total')->groupBy('client_id')->orderByDesc('total')->first();

        if (! $top || ! $top->client_id) {
            return null;
        }
        $user = User::find($top->client_id);

        return $user ? ['name' => trim($user->name.' '.($user->apellido ?? '')), 'username' => $user->username, 'total' => (float) $top->total] : null;
    }

    public static function globalTopAgent(?Collection $lines = null): ?array
    {
        $lineIds = $lines ? $lines->pluck('id')->all() : Line::where('status', 'active')->pluck('id')->all();
        if (empty($lineIds)) {
            return null;
        }

        $month = now()->month;
        $year = now()->year;
        [$monthFn, $yearFn] = self::dateFunctions();

        $top = Sale::whereIn('line_id', $lineIds)->whereRaw("{$monthFn} = ?", [$month])->whereRaw("{$yearFn} = ?", [$year])
            ->selectRaw('agent_id, SUM(monto_fichas) as total')->groupBy('agent_id')->orderByDesc('total')->first();

        if (! $top || ! $top->agent_id) {
            return null;
        }
        $agent = Agent::find($top->agent_id);

        return $agent ? ['name' => trim($agent->name.' '.($agent->apellido ?? '')), 'username' => $agent->username, 'total' => (float) $top->total] : null;
    }

    public static function globalSalesSummary(?Collection $lines = null): array
    {
        $lineIds = $lines ? $lines->pluck('id')->all() : Line::where('status', 'active')->pluck('id')->all();
        if (empty($lineIds)) {
            return ['transactions' => 0, 'avg_ticket' => 0, 'unique_clients' => 0];
        }

        $month = now()->month;
        $year = now()->year;
        [$monthFn, $yearFn] = self::dateFunctions();

        $query = Sale::whereIn('line_id', $lineIds)->whereRaw("{$monthFn} = ?", [$month])->whereRaw("{$yearFn} = ?", [$year]);

        $transactions = (clone $query)->count();
        $total = (clone $query)->sum('monto_fichas');

        return ['transactions' => $transactions, 'avg_ticket' => $transactions > 0 ? $total / $transactions : 0, 'unique_clients' => (clone $query)->distinct('client_id')->count('client_id')];
    }

    public static function globalDailySales(int $days = 30, ?Collection $lines = null): array
    {
        $lineIds = $lines ? $lines->pluck('id')->all() : Line::where('status', 'active')->pluck('id')->all();
        if (empty($lineIds)) {
            return ['labels' => [], 'data' => []];
        }

        $startDate = now()->subDays($days)->startOfDay();
        $dailyData = Sale::whereIn('line_id', $lineIds)->where('fecha_inicio', '>=', $startDate)
            ->selectRaw(self::dateFn().' as day, SUM(monto_fichas) as total')->groupBy('day')->orderBy('day')->get();

        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $found = $dailyData->firstWhere('day', $date);
            $data[] = $found ? (float) $found->total : 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public static function globalPlatformComparison(?Collection $lines = null): array
    {
        $lineIds = $lines ? $lines->pluck('id')->all() : Line::where('status', 'active')->pluck('id')->all();
        if (empty($lineIds)) {
            return ['labels' => [], 'data' => []];
        }

        $month = now()->month;
        $year = now()->year;
        [$monthFn, $yearFn] = self::dateFunctions();

        $platforms = Sale::whereIn('line_id', $lineIds)
            ->whereRaw("{$monthFn} = ?", [$month])
            ->whereRaw("{$yearFn} = ?", [$year])
            ->selectRaw('platform_id, SUM(monto_fichas) as total')
            ->groupBy('platform_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];
        foreach ($platforms as $p) {
            $platform = Platform::find($p->platform_id);
            $labels[] = $platform ? $platform->name : 'Unknown';
            $data[] = (float) $p->total;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public static function globalLineComparison(?Collection $lines = null): array
    {
        $lineIds = $lines ? $lines->pluck('id')->all() : Line::where('status', 'active')->pluck('id')->all();
        if (empty($lineIds)) {
            return ['labels' => [], 'data' => []];
        }

        $month = now()->month;
        $year = now()->year;
        [$monthFn, $yearFn] = self::dateFunctions();

        $linesData = Sale::whereIn('line_id', $lineIds)
            ->whereRaw("{$monthFn} = ?", [$month])
            ->whereRaw("{$yearFn} = ?", [$year])
            ->selectRaw('line_id, SUM(monto_fichas) as total')
            ->groupBy('line_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];
        foreach ($linesData as $l) {
            $line = Line::find($l->line_id);
            $labels[] = $line ? ($line->icon ?? '●').' '.substr($line->name, 0, 10) : 'Unknown';
            $data[] = (float) $l->total;
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
