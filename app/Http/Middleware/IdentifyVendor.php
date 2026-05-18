<?php

namespace App\Http\Middleware;

use App\Models\Vendor;
use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->hasRole(Roles::ADMIN)) {
                // El Admin puede ver todo (active_vendor_id = null) 
                // o puede elegir "actuar" como un vendor específico.
                if (!$request->session()->has('active_vendor_id')) {
                    // Por defecto el admin ve todo (global)
                }
            } else {
                // Cajeros, Agentes y Clientes están restringidos a su vendor_id
                if ($user->vendor_id && Vendor::query()->whereKey($user->vendor_id)->where('is_active', true)->exists()) {
                    session(['active_vendor_id' => $user->vendor_id]);
                } else {
                    $request->session()->forget('active_vendor_id');

                    if ($user->hasRole(Roles::CAJERO)) {
                        abort(403, 'El vendor asignado no esta activo.');
                    }
                }
            }
        }

        return $next($request);
    }
}
