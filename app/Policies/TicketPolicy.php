<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Support\Roles;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::AGENTE) || $user->hasRole(Roles::CAJERO);
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        if ($user->vendor_id && $ticket->vendor_id) {
            return $user->vendor_id === $ticket->vendor_id;
        }

        return false;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        return $user->vendor_id === $ticket->vendor_id;
    }

    public function close(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        return $user->vendor_id === $ticket->vendor_id;
    }
}
