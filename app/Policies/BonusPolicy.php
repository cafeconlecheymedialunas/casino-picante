<?php

namespace App\Policies;

use App\Models\Bonus;
use App\Models\User;
use App\Support\Roles;

class BonusPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Bonus $bonus): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        if ($user->vendor_id && $bonus->vendor_id) {
            return $user->vendor_id === $bonus->vendor_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function update(User $user, Bonus $bonus): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function delete(User $user, Bonus $bonus): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }
}
