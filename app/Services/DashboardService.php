<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\Workplace;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        return [
            'metrics' => [
                'workplaces' => Workplace::query()->whereNull('archived_at')->count(),
                'equipment' => Equipment::query()->whereNull('archived_at')->count(),
                'inspections' => Inspection::query()->count(),
            ],
            'recentWorkplaces' => Workplace::query()
                ->whereNull('archived_at')
                ->withCount(['equipment' => fn ($query) => $query->whereNull('archived_at')])
                ->latest()
                ->take(5)
                ->get(),
            'recentEquipment' => Equipment::query()
                ->whereNull('archived_at')
                ->with('workplace')
                ->latest()
                ->take(6)
                ->get(),
            'recentInspections' => Inspection::query()
                ->with(['equipment', 'template'])
                ->latest('inspected_at')
                ->take(6)
                ->get(),
        ];
    }
}
