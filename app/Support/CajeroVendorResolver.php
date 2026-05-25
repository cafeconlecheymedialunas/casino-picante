<?php

namespace App\Support;

use App\Models\User;
use App\Models\Vendor;

final class CajeroVendorResolver
{
    public static function activeVendorFor(User $user): ?Vendor
    {
        if (! $user->hasRole(Roles::CAJERO)) {
            return null;
        }

        $vendor = Vendor::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (! $vendor && $user->vendor_id) {
            $vendor = Vendor::query()
                ->whereKey($user->vendor_id)
                ->where('is_active', true)
                ->first();
        }

        if ($vendor && (int) $user->vendor_id !== (int) $vendor->id) {
            $user->forceFill(['vendor_id' => $vendor->id])->save();
        }

        return $vendor;
    }
}
