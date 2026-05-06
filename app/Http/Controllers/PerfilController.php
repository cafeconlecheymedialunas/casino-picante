<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAgent = session('active_agent_id') !== null;

        if ($isAgent && ! $user) {
            $agentId = session('active_agent_id');
            $agent = Agent::find($agentId);
            if ($agent) {
                return view('perfil.index', ['user' => $agent, 'isAgent' => true]);
            }
        }

        if ($user) {
            return view('perfil.index', ['user' => $user, 'isAgent' => false]);
        }

        return view('perfil.index', ['user' => null, 'isAgent' => false]);
    }

    public function update(Request $request)
    {
        $isAgent = session('active_agent_id') !== null;

        $availableAvatars = $this->getAvailableAvatars();

        if ($isAgent) {
            $agentId = session('active_agent_id');
            $agent = Agent::find($agentId);

            if ($agent) {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'avatar' => 'sometimes|string|in:'.implode(',', $availableAvatars),
                ]);

                $agent->update([
                    'name' => $request->name,
                    'avatar' => $request->avatar ?? $agent->avatar,
                ]);

                return back()->with('message', 'Perfil actualizado correctamente');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'avatar' => 'sometimes|string|in:'.implode(',', $availableAvatars),
        ]);

        $user = $request->user();
        $user->update([
            'name' => $request->name,
            'apellido' => $request->apellido,
            'avatar' => $request->avatar ?? $user->avatar,
        ]);

        return back()->with('message', 'Perfil actualizado correctamente');
    }

    public function updatePassword(Request $request)
    {
        $isAgent = session('active_agent_id') !== null;

        if ($isAgent) {
            $agentId = session('active_agent_id');
            $agent = Agent::find($agentId);

            if ($agent) {
                $request->validate([
                    'current_password' => ['required', 'current_password'],
                    'password' => ['required', Password::defaults(), 'confirmed'],
                ]);

                $agent->update([
                    'password' => Hash::make($request->password),
                ]);

                return back()->with('message', 'Contraseña actualizada correctamente');
            }
        }

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('message', 'Contraseña actualizada correctamente');
    }

    private function getAvailableAvatars(): array
    {
        return [
            'avatar_nova', 'avatar_blaze', 'avatar_onyx', 'avatar_vega',
            'avatar_luna', 'avatar_orion', 'avatar_pixel', 'avatar_arcade',
            'avatar_neon', 'avatar_terra', 'avatar_sol', 'avatar_zen',
            'avatar_ember', 'avatar_cobalt', 'avatar_iris', 'avatar_mint',
        ];
    }

    public static function getAvailableAvatarsStatic()
    {
        $controller = new static;

        return $controller->getAvailableAvatars();
    }
}
