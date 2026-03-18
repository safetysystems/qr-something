<?php

namespace App\Policies;

use App\Models\InspectionTemplate;
use App\Models\User;
use App\Models\Workplace;

class InspectionTemplatePolicy
{
    public function viewAny(User $user, Workplace $workplace): bool
    {
        return $user->canAccessWorkplace($workplace);
    }

    public function view(User $user, InspectionTemplate $inspectionTemplate): bool
    {
        return $user->canAccessWorkplace($inspectionTemplate->workplace);
    }

    public function create(User $user, Workplace $workplace): bool
    {
        return $user->is_super_admin && $user->canAccessWorkplace($workplace);
    }

    public function update(User $user, InspectionTemplate $inspectionTemplate): bool
    {
        return $user->is_super_admin && $user->canAccessWorkplace($inspectionTemplate->workplace);
    }

    public function delete(User $user, InspectionTemplate $inspectionTemplate): bool
    {
        return $user->is_super_admin && $user->canAccessWorkplace($inspectionTemplate->workplace);
    }
}
