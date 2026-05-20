<?php

namespace App\Policies;

use App\Models\Raffle;
use App\Models\User;
use App\Support\Roles;

class RafflePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Raffle $raffle): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        if ($user->vendor_id && $raffle->vendor_id) {
            return $user->vendor_id === $raffle->vendor_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function update(User $user, Raffle $raffle): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function delete(User $user, Raffle $raffle): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }
}
