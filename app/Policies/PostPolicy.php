<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Support\Roles;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        if ($user->vendor_id && $post->vendor_id) {
            return $user->vendor_id === $post->vendor_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::AGENTE);
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        return $user->vendor_id === $post->vendor_id;
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }

        return $user->vendor_id === $post->vendor_id;
    }
}
