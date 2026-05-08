<?php

namespace App\Http\Middleware;

use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasRole(Roles::ADMIN)) {
            abort(403, 'Solo el administrador general puede acceder a esta sección.');
        }

        return $next($request);
    }
}
