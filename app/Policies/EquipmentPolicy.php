<?php

namespace App\Policies;

use App\Models\Equipment;
use App\Models\User;
use App\Models\Workplace;

class EquipmentPolicy
{
    public function view(User $user, Equipment $equipment): bool
    {
        return $user->canAccessWorkplace($equipment->workplace);
    }

    public function create(User $user, Workplace $workplace): bool
    {
        return $user->canAccessWorkplace($workplace) && is_null($workplace->archived_at);
    }

    public function update(User $user, Equipment $equipment): bool
    {
        return $user->canAccessWorkplace($equipment->workplace) && is_null($equipment->archived_at);
    }

    public function delete(User $user, Equipment $equipment): bool
    {
        return $user->canAccessWorkplace($equipment->workplace) && is_null($equipment->archived_at);
    }
}
