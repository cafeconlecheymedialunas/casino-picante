<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\HomeSection;
use App\Models\Post;
use App\Models\Raffle;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function saveSection(Request $request)
    {
        abort_unless(session('active_vendor_id'), 403, 'Selecciona un vendor antes de editar la home.');

        $key = $request->input('section_key');
        if (! in_array($key, ['sorteo', 'bonos', 'blog'])) {
            return response()->json(['message' => 'Sección inválida'], 400);
        }

        $parseToArray = function ($str, ?string $model = null) {
            if (empty($str)) {
                return null;
            }
            $arr = array_filter(array_map('trim', explode(',', $str)));
            if ($model && session('active_vendor_id')) {
                $ids = collect($arr)
                    ->map(fn ($id) => (int) $id)
                    ->filter()
                    ->unique()
                    ->values();

                $arr = $model::whereIn('id', $ids)
                    ->where('vendor_id', (int) session('active_vendor_id'))
                    ->pluck('id')
                    ->map(fn ($id) => (string) $id)
                    ->all();
            }

            return count($arr) ? $arr : null;
        };

        HomeSection::updateOrCreate(
            ['vendor_id' => (int) session('active_vendor_id'), 'section_key' => $key],
            [
                'raffle_ids' => $key === 'sorteo' ? $parseToArray($request->input('raffle_ids', ''), Raffle::class) : null,
                'bonus_ids' => $key === 'bonos' ? $parseToArray($request->input('bonus_ids', ''), Bonus::class) : null,
                'post_ids' => $key === 'blog' ? $parseToArray($request->input('post_ids', ''), Post::class) : null,
            ]
        );

        return response()->json(['message' => 'Sección guardada correctamente']);
    }
}
