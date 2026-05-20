<?php

namespace App\Http\Middleware;

use App\Models\Vendor;
use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolvePublicVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        $vendor = $request->route('vendor');

        if (! $vendor instanceof Vendor) {
            $vendor = Vendor::query()
                ->where('slug', (string) $vendor)
                ->where('is_active', true)
                ->firstOrFail();
        }

        abort_unless($vendor->is_active, 404);

        $user = $request->user();
        if ($user && ! $user->hasRole(Roles::ADMIN)) {
            abort_unless($user->vendor_id && (int) $user->vendor_id === (int) $vendor->id, 403);
        }

        session(['active_vendor_id' => $vendor->id]);
        view()->share('publicVendor', $vendor);

        return $next($request);
    }
}
