<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workplace;

class WorkplaceContextService
{
    public function current(User $user): Workplace
    {
        return $user->workplaces()
            ->whereNull('workplaces.archived_at')
            ->orderBy('workplaces.name')
            ->firstOrFail();
    }
}
