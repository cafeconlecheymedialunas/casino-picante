<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use App\Support\Roles;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::AGENTE) || $user->hasRole(Roles::CAJERO);
    }

    public function view(User $user, Sale $sale): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        if ($user->vendor_id && $sale->vendor_id) {
            return $user->vendor_id === $sale->vendor_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::AGENTE) || $user->hasRole(Roles::CAJERO);
    }
}
