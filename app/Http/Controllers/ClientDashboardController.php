<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentResource;
use App\Http\Resources\InspectionResource;
use App\Http\Resources\WorkplaceResource;
use App\Services\ClientDashboardService;
use App\Services\WorkplaceContextService;
use Inertia\Inertia;
use Inertia\Response;

class ClientDashboardController extends Controller
{
    public function __construct(
        private readonly WorkplaceContextService $workplaceContextService,
        private readonly ClientDashboardService $clientDashboardService,
    ) {
    }

    public function __invoke(): Response
    {
        $user = request()->user();
        $workplace = $this->workplaceContextService->current($user);
        $summary = $this->clientDashboardService->summary($workplace);

        return Inertia::render('Client/Dashboard', [
            'workplace' => WorkplaceResource::make(
                $workplace->loadCount(['equipment' => fn ($query) => $query->whereNull('archived_at')])
            )->resolve(request()),
            'metrics' => $summary['metrics'],
            'recentEquipment' => $this->resourceCollectionData(
                $summary['recentEquipment'],
                EquipmentResource::class
            ),
            'recentInspections' => $this->resourceCollectionData(
                $summary['recentInspections'],
                InspectionResource::class
            ),
            'links' => [
                'equipment' => route('client.equipment.index'),
                'create_equipment' => route('client.equipment.create'),
            ],
        ]);
    }
}
