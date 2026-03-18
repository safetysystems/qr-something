<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workplace;

class WorkplacePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin;
    }

    public function view(User $user, Workplace $workplace): bool
    {
        return $user->canAccessWorkplace($workplace);
    }

    public function create(User $user): bool
    {
        return $user->is_super_admin;
    }

    public function update(User $user, Workplace $workplace): bool
    {
        return $user->is_super_admin;
    }

    public function delete(User $user, Workplace $workplace): bool
    {
        return $user->is_super_admin;
    }
}
