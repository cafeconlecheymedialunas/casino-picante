<?php

namespace App\Http\Middleware;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeLine
{
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $agentId = session('active_agent_id');

        // No agent in session → admin/bypass mode, allow through
        if (! $agentId) {
            return $next($request);
        }

        $agent = Agent::find($agentId);
        if (! $agent || $agent->status !== 'active') {
            abort(403, 'Agente inactivo o no encontrado.');
        }

        // Resolve active line (auto-assign first available if not in session)
        $lineId = session('active_line_id');
        if (! $lineId) {
            $first = LineAgent::where('agent_id', $agentId)->where('is_active', true)->first();
            if (! $first) {
                abort(403, 'No tenés líneas asignadas.');
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
                abort(403, 'No pertenecés a ninguna línea activa.');
            }
            session(['active_line_id' => $first->line_id]);
            return redirect($request->url());
        }

        // Check specific permission if provided
        if ($permission && ! $lineAgent->hasPermission($permission)) {
            abort(403, "Sin permiso: {$permission}");
        }

        // Share context with all views and Livewire components
        view()->share('activeLine', $lineAgent->line);
        view()->share('currentLineAgent', $lineAgent);

        return $next($request);
    }
}
