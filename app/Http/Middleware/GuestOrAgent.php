<?php

namespace App\Http\Middleware;

use App\Models\Agent;
use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestOrAgent
{
    public function handle(Request $request, Closure $next): Response
    {
        // Clear stale agent session before any check
        if (session('active_agent_id') && ! Agent::whereKey(session('active_agent_id'))->exists()) {
            session()->forget(['active_agent_id', 'active_line_id']);
        }

        if (! auth()->check() && ! session('active_agent_id')) {
            return $next($request);
        }

        if (auth()->user()?->hasRole(Roles::CLIENTE)) {
            return redirect()->route('perfil');
        }

        return redirect()->route('dashboard');
    }
}
