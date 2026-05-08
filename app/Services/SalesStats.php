<?php

namespace App\Services;

use App\Models\Line;
use App\Models\Platform;
use App\Models\Sale;
use App\Support\LineRoles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesStats
{
    public static function lineStats(Line $line, ?int $month = null, ?int $year = null): array
    {
        $month ??= now()->month;
        $year  ??= now()->year;

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
                'name'       => trim(($lineAgent->agent?->name ?? 'Encargado').' '.($lineAgent->agent?->apellido ?? '')),
                'porcentaje' => (float) ($lineAgent->porcentaje_ganancia ?? 0),
                'ganancia'   => $monthTotal * ((float) ($lineAgent->porcentaje_ganancia ?? 0) / 100),
            ])
            ->values();

        return compact('bestMonth', 'bestPlatform', 'lastMonths', 'earnings', 'monthTotal');
    }

    public static function bestSellingLineOfMonth(?int $month = null, ?int $year = null): ?array
    {
        $month ??= now()->month;
        $year  ??= now()->year;

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
            'id'         => $line->id,
            'name'       => $line->name,
            'icon'       => $line->icon ?: '●',
            'best_sales' => (float) $sale->total,
        ] : null;
    }

    public static function globalMonthStats(Collection $lines, ?int $month = null, ?int $year = null): array
    {
        $month   ??= now()->month;
        $year    ??= now()->year;
        $lineIds = $lines->pluck('id')->all();

        [$monthFn, $yearFn] = self::dateFunctions();

        $query = Sale::whereIn('line_id', $lineIds)
            ->whereRaw("{$monthFn} = ?", [$month])
            ->whereRaw("{$yearFn} = ?", [$year]);

        $total         = (float) (clone $query)->sum('monto_fichas');
        $records       = (clone $query)->count();
        $lineCount     = (clone $query)->distinct('line_id')->count('line_id');
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
            'total'         => $total,
            'records'       => $records,
            'lineCount'     => $lineCount,
            'platformCount' => $platformCount,
            'bestLine'      => $bestLine ? [
                'line'  => Line::find($bestLine->line_id),
                'total' => (float) $bestLine->total,
            ] : null,
            'bestPlatform'  => $bestPlatform ? [
                'platform' => Platform::find($bestPlatform->platform_id),
                'total'    => (float) $bestPlatform->total,
            ] : null,
        ];
    }

    private static function dateFunctions(): array
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return ["CAST(strftime('%m', fecha) AS INTEGER)", "CAST(strftime('%Y', fecha) AS INTEGER)"];
        }

        return ['MONTH(fecha)', 'YEAR(fecha)'];
    }
}
