<?php

namespace App\Policies;

use App\Models\Line;
use App\Models\User;
use App\Support\Roles;

class LinePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::AGENTE) || $user->hasRole(Roles::CAJERO);
    }

    public function view(User $user, Line $line): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        if ($user->vendor_id && $line->vendor_id) {
            return $user->vendor_id === $line->vendor_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function update(User $user, Line $line): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        return $user->vendor_id === $line->vendor_id;
    }

    public function delete(User $user, Line $line): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }
}
