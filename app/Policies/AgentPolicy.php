<?php

namespace App\Policies;

use App\Models\Agent;
use App\Models\User;
use App\Support\Roles;

class AgentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::AGENTE);
    }

    public function view(User $user, Agent $agent): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        if ($user->hasRole(Roles::AGENTE)) {
            return $user->vendor_id === $agent->vendor_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function update(User $user, Agent $agent): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        return $user->vendor_id === $agent->vendor_id;
    }

    public function assign(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function managePermissions(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }

    public function delete(User $user, Agent $agent): bool
    {
        return $user->hasRole(Roles::ADMIN);
    }
}
