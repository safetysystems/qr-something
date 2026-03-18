<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentResource;
use App\Http\Resources\InspectionResource;
use App\Http\Resources\WorkplaceResource;
use App\Services\DashboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function __invoke(): Response
    {
        $summary = $this->dashboardService->summary();

        return Inertia::render('Dashboard/Index', [
            'metrics' => $summary['metrics'],
            'recentWorkplaces' => $this->resourceCollectionData(
                $summary['recentWorkplaces'],
                WorkplaceResource::class
            ),
            'recentEquipment' => $this->resourceCollectionData(
                $summary['recentEquipment'],
                EquipmentResource::class
            ),
            'recentInspections' => $this->resourceCollectionData(
                $summary['recentInspections'],
                InspectionResource::class
            ),
            'links' => [
                'create_workplace' => route('workplaces.create'),
                'workplaces' => route('workplaces.index'),
                'inspections' => null,
            ],
        ]);
    }
}
