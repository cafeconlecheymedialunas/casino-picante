<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\HomeSection;
use App\Models\Line;
use App\Models\Post;
use App\Models\Raffle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function saveSection(Request $request)
    {
        $key = $request->input('section_key');
        if (! in_array($key, ['sorteo', 'bonos', 'blog', 'lineas'])) {
            return response()->json(['message' => 'Seccion invalida'], 400);
        }

        HomeSection::withoutGlobalScopes()->updateOrCreate(
            ['vendor_id' => null, 'section_key' => $key],
            [
                'vendor_id' => null,
                'raffle_ids' => $key === 'sorteo' ? $this->parseIds($request->input('raffle_ids', ''), Raffle::class) : null,
                'bonus_ids' => $key === 'bonos' ? $this->parseIds($request->input('bonus_ids', ''), Bonus::class) : null,
                'post_ids' => $key === 'blog' ? $this->parseIds($request->input('post_ids', ''), Post::class) : null,
                'line_ids' => $key === 'lineas' ? $this->parseIds($request->input('line_ids', ''), Line::class) : null,
            ]
        );

        return response()->json(['message' => 'Seccion guardada correctamente']);
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function parseIds(string $value, string $model): ?array
    {
        $ids = collect(explode(',', $value))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return null;
        }

        $validIds = $model::withoutGlobalScopes()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        return $validIds ?: null;
    }
}
