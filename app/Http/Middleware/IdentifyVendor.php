<?php

namespace App\Http\Middleware;

use App\Models\Line;
use App\Models\Vendor;
use App\Support\CajeroVendorResolver;
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
                } elseif ($activeVendorId && $request->session()->get('active_line_id')) {
                    $lineBelongsToVendor = Line::query()
                        ->whereKey((int) $request->session()->get('active_line_id'))
                        ->where('vendor_id', (int) $activeVendorId)
                        ->where('status', 'active')
                        ->exists();

                    if (! $lineBelongsToVendor) {
                        $request->session()->forget('active_line_id');
                    }
                }
            } elseif ($user->hasRole(Roles::CAJERO)) {
                $vendor = CajeroVendorResolver::activeVendorFor($user);

                if (! $vendor) {
                    $request->session()->forget(['active_vendor_id', 'active_line_id']);
                    abort(403, 'El vendor asignado no esta activo.');
                }

                $previousVendorId = session('active_vendor_id');
                session(['active_vendor_id' => $vendor->id]);

                if ($previousVendorId && (int) $previousVendorId !== (int) $vendor->id) {
                    Log::channel('daily')->info('Vendor auto-corrected for cajero user', [
                        'user_id' => $user->id,
                        'previous_vendor_id' => $previousVendorId,
                        'correct_vendor_id' => $vendor->id,
                    ]);
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
                    $vendor = Vendor::query()
                        ->where('user_id', $user->id)
                        ->where('is_active', true)
                        ->first();

                    if ($vendor) {
                        $user->forceFill(['vendor_id' => $vendor->id])->save();
                        session(['active_vendor_id' => $vendor->id]);

                        return $next($request);
                    }

                    $request->session()->forget('active_vendor_id');

                }
            }
        }

        return $next($request);
    }
}
