<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function saveSection(Request $request)
    {
        $key = $request->input('section_key');
        if (! in_array($key, ['sorteo', 'bonos', 'blog'])) {
            return response()->json(['message' => 'Sección inválida'], 400);
        }

        $parseToArray = function ($str) {
            if (empty($str)) {
                return null;
            }
            $arr = array_filter(array_map('trim', explode(',', $str)));

            return count($arr) ? $arr : null;
        };

        HomeSection::updateOrCreate(
            ['section_key' => $key],
            [
                'raffle_ids' => $key === 'sorteo' ? $parseToArray($request->input('raffle_ids', '')) : null,
                'bonus_ids' => $key === 'bonos' ? $parseToArray($request->input('bonus_ids', '')) : null,
                'post_ids' => $key === 'blog' ? $parseToArray($request->input('post_ids', '')) : null,
            ]
        );

        return response()->json(['message' => 'Sección guardada correctamente']);
    }
}
