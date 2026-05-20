<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use App\Support\Roles;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function view(User $user, Vendor $vendor): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        return $user->vendor_id === $vendor->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function update(User $user, Vendor $vendor): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }
}
