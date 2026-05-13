<?php

namespace App\Http\Middleware;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeLine
{
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        if (! auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = $request->user();

        if ($user->hasRole(Roles::ADMIN)) {
            return $next($request);
        }

        if (! $user->hasRole(Roles::AGENTE)) {
            abort(403, 'No tenes acceso al panel.');
        }

        // Validate session agent_id belongs to user
        $sessionAgentId = session('active_agent_id');
        if ($sessionAgentId && ! Agent::where('id', $sessionAgentId)->where('user_id', $user->id)->exists()) {
            session()->forget(['active_agent_id', 'active_line_id']);
            $sessionAgentId = null;
        }

        $agentId = $sessionAgentId ?: $user->agent?->id;
        if ($agentId) {
            session(['active_agent_id' => $agentId]);
        }

        $agent = Agent::where('id', $agentId)->where('user_id', $user->id)->first();
        if (! $agent || $agent->status !== 'active') {
            abort(403, 'Agente inactivo o no encontrado.');
        }

        // Resolve active line (auto-assign first available if not in session)
        $lineId = session('active_line_id');
        if (! $lineId) {
            $first = LineAgent::where('agent_id', $agentId)->where('is_active', true)->first();
            if (! $first) {
                return redirect()->route('perfil')->with('error', 'No tenés líneas asignadas.');
            }
            session(['active_line_id' => $first->line_id]);
            $lineId = $first->line_id;
        }

        // Verify agent belongs to this line
        $lineAgent = LineAgent::where('line_id', $lineId)
            ->where('agent_id', $agentId)
            ->where('is_active', true)
            ->first();

        if (! $lineAgent) {
            // Reassign to the agent's first active line
            $first = LineAgent::where('agent_id', $agentId)->where('is_active', true)->first();
            if (! $first) {
                return redirect()->route('perfil')->with('error', 'No pertenecés a ninguna línea activa.');
            }
            session(['active_line_id' => $first->line_id]);

            return redirect($request->url());
        }

        // Check specific permission if provided. Multiple permissions mean "any of these".
        $permissions = $permission
            ? preg_split('/[|,]/', $permission, flags: PREG_SPLIT_NO_EMPTY)
            : [];

        if ($permissions && ! collect($permissions)->contains(fn (string $perm) => $lineAgent->hasPermission(trim($perm)))) {
            abort(403, "Sin permiso: {$permission}");
        }

        // Share context with all views and Livewire components
        view()->share('activeLine', $lineAgent->line);
        view()->share('currentLineAgent', $lineAgent);

        return $next($request);
    }
}
