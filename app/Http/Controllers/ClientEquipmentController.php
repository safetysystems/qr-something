<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentResource;
use App\Http\Resources\InspectionResource;
use App\Http\Resources\WorkplaceResource;
use App\Models\Equipment;
use App\Services\EquipmentService;
use App\Services\InspectionService;
use App\Services\WorkplaceContextService;
use Inertia\Inertia;
use Inertia\Response;

class ClientEquipmentController extends Controller
{
    public function __construct(
        private readonly WorkplaceContextService $workplaceContextService,
        private readonly EquipmentService $equipmentService,
        private readonly InspectionService $inspectionService,
    ) {
    }

    public function index(): Response
    {
        $workplace = $this->workplaceContextService->current(request()->user());
        $equipment = $this->equipmentService->paginateForWorkplace($workplace);

        return Inertia::render('Client/Equipment/Index', [
            'workplace' => WorkplaceResource::make(
                $workplace->loadCount(['equipment' => fn ($query) => $query->whereNull('archived_at')])
            )->resolve(request()),
            'equipment' => $this->paginatedResourceData($equipment, EquipmentResource::class),
        ]);
    }

    public function show(Equipment $equipment): Response
    {
        $workplace = $this->workplaceContextService->current(request()->user());

        abort_unless($equipment->workplace_id === $workplace->id, 404);

        $this->authorize('view', $equipment);

        $equipment = $this->equipmentService->detail($equipment);
        $recentInspections = $this->inspectionService->recentForEquipment($equipment);

        return Inertia::render('Client/Equipment/Show', [
            'workplace' => WorkplaceResource::make($workplace)->resolve(request()),
            'equipment' => EquipmentResource::make($equipment)->resolve(request()),
            'recentInspections' => $this->resourceCollectionData(
                $recentInspections,
                InspectionResource::class
            ),
        ]);
    }
}
