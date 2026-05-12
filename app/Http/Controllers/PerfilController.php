<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $agent = null;

        if (session('active_agent_id') && $user) {
            $agent = Agent::where('id', session('active_agent_id'))
                ->where('user_id', $user->id)
                ->first();
        }

        if ($agent) {
            return view('perfil.index', ['user' => $agent, 'isAgent' => true]);
        }

        if ($user) {
            return view('perfil.index', ['user' => $user, 'isAgent' => false]);
        }

        return view('perfil.index', ['user' => null, 'isAgent' => false]);
    }

    public function update(Request $request)
    {
        $isAgent = session('active_agent_id') !== null;
        $user = $request->user();
        $avatarRule = ['sometimes', 'string', 'regex:/^avatar_[A-Za-z0-9_-]{1,80}$/'];

        if ($isAgent) {
            $agent = Agent::where('id', session('active_agent_id'))
                ->where('user_id', $user->id)
                ->first();

            if ($agent) {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'apellido' => 'nullable|string|max:255',
                    'email' => 'nullable|email|max:255',
                    'username' => 'nullable|string|max:255',
                    'phone' => 'nullable|string|max:50',
                    'avatar' => $avatarRule,
                ]);

                DB::transaction(function () use ($agent, $user, $request) {
                    $agent->update([
                        'name' => $request->name,
                        'apellido' => $request->apellido ?? $agent->apellido,
                        'email' => $request->email ?? $agent->email,
                        'username' => $request->username ?? $agent->username,
                        'phone' => $request->phone ?? $agent->phone,
                        'avatar' => $request->avatar ?? $agent->avatar,
                    ]);

                    $user->update([
                        'name' => $request->name,
                        'apellido' => $request->apellido ?? $user->apellido,
                        'email' => $request->email ?? $user->email,
                        'username' => $request->username ?? $user->username,
                        'phone' => $request->phone ?? $user->phone,
                        'avatar' => $request->avatar ?? $user->avatar,
                    ]);
                });

                return back()->with('message', 'Perfil actualizado correctamente');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'username' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'contact' => 'nullable|string|max:500',
            'avatar' => $avatarRule,
        ]);

        $user = $request->user();
        $user->update([
            'name' => $request->name,
            'apellido' => $request->apellido ?? $user->apellido,
            'email' => $request->email ?? $user->email,
            'username' => $request->username ?? $user->username,
            'phone' => $request->phone ?? $user->phone,
            'contact' => $request->contact ?? $user->contact,
            'avatar' => $request->avatar ?? $user->avatar,
        ]);

        return back()->with('message', 'Perfil actualizado correctamente');
    }

    public function updatePassword(Request $request)
    {
        $isAgent = session('active_agent_id') !== null;
        $user = $request->user();

        if ($isAgent) {
            $agent = Agent::where('id', session('active_agent_id'))
                ->where('user_id', $user->id)
                ->first();

            if (! $agent) {
                return back()->with('error', 'Agente no encontrado.');
            }

            $request->validate([
                'current_password' => 'required|string',
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $currentHash = $user->password ?: $agent->password;
            if (! Hash::check($request->current_password, $currentHash)) {
                return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
            }

            $password = Hash::make($request->password);
            DB::transaction(function () use ($agent, $user, $password) {
                $agent->update(['password' => $password]);
                $user->update(['password' => $password]);
            });

            return back()->with('message', 'Contraseña actualizada correctamente');
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
}
