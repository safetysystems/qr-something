<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\InspectionTemplate;
use App\Models\Workplace;

class ClientDashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(Workplace $workplace): array
    {
        return [
            'metrics' => [
                'equipment' => Equipment::query()
                    ->where('workplace_id', $workplace->id)
                    ->whereNull('archived_at')
                    ->count(),
                'inspections' => Inspection::query()
                    ->where('workplace_id', $workplace->id)
                    ->count(),
                'templates' => InspectionTemplate::query()
                    ->where('workplace_id', $workplace->id)
                    ->whereNull('archived_at')
                    ->count(),
            ],
            'recentEquipment' => Equipment::query()
                ->where('workplace_id', $workplace->id)
                ->whereNull('archived_at')
                ->with('workplace')
                ->latest()
                ->take(6)
                ->get(),
            'recentInspections' => Inspection::query()
                ->where('workplace_id', $workplace->id)
                ->with(['equipment', 'template', 'inspector'])
                ->latest('inspected_at')
                ->take(6)
                ->get(),
        ];
    }
}
