<?php

namespace App\Http\Middleware;

use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrCajero
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->hasRole(Roles::ADMIN) && ! $user?->hasRole(Roles::CAJERO)) {
            abort(403, 'Solo administradores y cajeros pueden acceder a esta seccion.');
        }

        return $next($request);
    }
}
