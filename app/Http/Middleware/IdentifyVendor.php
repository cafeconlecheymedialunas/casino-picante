<?php

namespace App\Http\Middleware;

use App\Models\Vendor;
use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IdentifyVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->hasRole(Roles::ADMIN)) {
                $activeVendorId = $request->session()->get('active_vendor_id');

                if ($activeVendorId && ! Vendor::query()->whereKey($activeVendorId)->where('is_active', true)->exists()) {
                    $request->session()->forget(['active_vendor_id', 'active_line_id']);
                }
            } else {
                if ($user->vendor_id && Vendor::query()->whereKey($user->vendor_id)->where('is_active', true)->exists()) {
                    $previousVendorId = session('active_vendor_id');
                    session(['active_vendor_id' => $user->vendor_id]);

                    if ($previousVendorId && (int) $previousVendorId !== (int) $user->vendor_id) {
                        Log::channel('daily')->info('Vendor auto-corrected for non-admin user', [
                            'user_id' => $user->id,
                            'role' => $user->role?->name,
                            'previous_vendor_id' => $previousVendorId,
                            'correct_vendor_id' => $user->vendor_id,
                        ]);
                    }
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
