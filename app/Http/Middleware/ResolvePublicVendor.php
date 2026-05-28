<?php

namespace App\Http\Middleware;

use App\Models\Vendor;
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

        $request->route()->setParameter('vendor', $vendor);
        view()->share('publicVendor', $vendor);

        // Set session so /registro knows which vendor the client is registering under
        $request->session()->put('active_vendor_id', $vendor->id);

        return $next($request);
    }
}
