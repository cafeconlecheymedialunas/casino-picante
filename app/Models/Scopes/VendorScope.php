<?php

namespace App\Models\Scopes;

use App\Models\Vendor;
use App\Support\Roles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VendorScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();

        if (! $vendorId = session('active_vendor_id')) {
            return;
        }

        $vendorId = (int) $vendorId;
        $guard = auth()->guard();

        if (! $guard->hasUser()) {
            if ($table !== 'users') {
                $builder->where($table.'.vendor_id', $vendorId);
            }

            return;
        }

        $user = $guard->user();

        if ($user->hasRole(Roles::ADMIN)) {
            return;
        }

        if ($user->vendor_id && (int) $user->vendor_id !== $vendorId) {
            $validVendor = Vendor::query()
                ->whereKey($user->vendor_id)
                ->where('is_active', true)
                ->exists();

            if ($validVendor) {
                session(['active_vendor_id' => $user->vendor_id]);
                $vendorId = (int) $user->vendor_id;
            } else {
                abort(403, 'No tenes acceso a este vendor.');
            }
        }

        if ($table === 'users') {
            $authUserId = (int) $user->id;
            $builder->where(function (Builder $query) use ($table, $vendorId, $authUserId): void {
                $query->where($table.'.vendor_id', $vendorId)
                    ->orWhere($table.'.id', $authUserId);
            });

            return;
        }

        $builder->where($table.'.vendor_id', $vendorId);
    }
}
