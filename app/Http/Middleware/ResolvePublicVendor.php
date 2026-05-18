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

        session(['active_vendor_id' => $vendor->id]);
        view()->share('publicVendor', $vendor);

        return $next($request);
    }
}
