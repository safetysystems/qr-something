<?php

namespace App\Policies;

use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\User;

class InspectionPolicy
{
    public function view(User $user, Inspection $inspection): bool
    {
        return $user->canAccessWorkplace($inspection->workplace);
    }

    public function create(User $user, Equipment $equipment): bool
    {
        return $user->is_super_admin && $user->canAccessWorkplace($equipment->workplace);
    }
}
