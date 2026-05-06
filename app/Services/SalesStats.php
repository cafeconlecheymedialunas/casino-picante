<?php

namespace App\Services;

use App\Models\Line;
use App\Models\Platform;
use App\Models\Sale;
use Illuminate\Support\Collection;

class SalesStats
{
    public static function lineStats(Line $line, ?int $month = null, ?int $year = null): array
    {
        $month ??= now()->month;
        $year ??= now()->year;

        $bestMonth = Sale::where('line_id', $line->id)
            ->selectRaw('mes, anio, SUM(monto_fichas) as total')
            ->groupBy('mes', 'anio')
            ->orderByDesc('total')
            ->first();

        $bestPlatform = Sale::where('line_id', $line->id)
            ->where('mes', $month)
            ->where('anio', $year)
            ->selectRaw('platform_id, SUM(monto_fichas) as total')
            ->groupBy('platform_id')
            ->orderByDesc('total')
            ->first();

        if ($bestPlatform) {
            $bestPlatform->setRelation('platform', Platform::find($bestPlatform->platform_id));
        }

        $lastMonths = Sale::where('line_id', $line->id)
            ->selectRaw('mes, anio, SUM(monto_fichas) as total')
            ->groupBy('mes', 'anio')
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->limit(3)
            ->get();

        $monthTotal = (float) Sale::where('line_id', $line->id)
            ->where('mes', $month)
            ->where('anio', $year)
            ->sum('monto_fichas');

        $lineAgents = $line->relationLoaded('lineAgents')
            ? $line->lineAgents
            : $line->lineAgents()->with('agent')->get();

        $earnings = $lineAgents
            ->where('role', 'encargado')
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

        $sale = Sale::query()
            ->join('lines', 'lines.id', '=', 'sales.line_id')
            ->where('lines.status', 'active')
            ->where('sales.mes', $month)
            ->where('sales.anio', $year)
            ->selectRaw('sales.line_id, SUM(sales.monto_fichas) as total')
            ->groupBy('sales.line_id')
            ->orderByDesc('total')
            ->first();

        if (! $sale) {
            return null;
        }

        $line = Line::find($sale->line_id);

        if (! $line) {
            return null;
        }

        return [
            'id' => $line->id,
            'name' => $line->name,
            'icon' => $line->icon ?: '●',
            'best_sales' => (float) $sale->total,
        ];
    }

    public static function globalMonthStats(Collection $lines, ?int $month = null, ?int $year = null): array
    {
        $month ??= now()->month;
        $year ??= now()->year;
        $lineIds = $lines->pluck('id')->all();

        $query = Sale::whereIn('line_id', $lineIds)
            ->where('mes', $month)
            ->where('anio', $year);

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
}
