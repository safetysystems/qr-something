<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Http\Resources\EquipmentResource;
use App\Http\Resources\InspectionResource;
use App\Http\Resources\InspectionTemplateResource;
use App\Http\Resources\WorkplaceResource;
use App\Models\Equipment;
use App\Models\Workplace;
use App\Services\EquipmentService;
use App\Services\EquipmentQrCodeService;
use App\Services\InspectionService;
use App\Services\InspectionTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class EquipmentController extends Controller
{
    public function __construct(
        private readonly EquipmentService $equipmentService,
        private readonly EquipmentQrCodeService $equipmentQrCodeService,
        private readonly InspectionService $inspectionService,
        private readonly InspectionTemplateService $inspectionTemplateService,
    ) {
    }

    public function create(Request $request, Workplace $workplace): InertiaResponse
    {
        $this->authorize('create', [Equipment::class, $workplace]);

        return Inertia::render('Equipment/Create', [
            'workplace' => WorkplaceResource::make($workplace)->resolve($request),
        ]);
    }

    public function store(StoreEquipmentRequest $request, Workplace $workplace): JsonResponse|RedirectResponse
    {
        $equipment = $this->equipmentService->create($workplace, $request->validated(), $request->user());

        if ($request->expectsJson()) {
            return EquipmentResource::make(
                $this->equipmentService->detail($equipment)
            )->additional([
                'message' => 'Equipment created successfully.',
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        }

        return redirect()
            ->route('workplaces.show', $workplace)
            ->with('status', 'Equipment created successfully.');
    }

    public function show(Request $request, Equipment $equipment): InertiaResponse|EquipmentResource
    {
        $this->authorize('view', $equipment);

        $equipment = $this->equipmentService->detail($equipment);
        $equipment = $this->equipmentQrCodeService->ensureGenerated($equipment);
        $inspectionTemplates = $this->inspectionTemplateService->availableForWorkplace($equipment->workplace);
        $recentInspections = $this->inspectionService->recentForEquipment($equipment);

        if ($request->expectsJson()) {
            return EquipmentResource::make($equipment);
        }

        return Inertia::render('Equipment/Show', [
            'equipment' => EquipmentResource::make($equipment)->resolve($request),
            'inspectionTemplates' => $this->resourceCollectionData($inspectionTemplates, InspectionTemplateResource::class),
            'recentInspections' => $this->resourceCollectionData($recentInspections, InspectionResource::class),
        ]);
    }

    public function edit(Request $request, Equipment $equipment): InertiaResponse
    {
        $this->authorize('update', $equipment);

        return Inertia::render('Equipment/Edit', [
            'equipment' => EquipmentResource::make(
                $equipment->load('workplace', 'creator')
            )->resolve($request),
        ]);
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment): EquipmentResource|RedirectResponse
    {
        $equipment = $this->equipmentService->update($equipment, $request->validated());

        if ($request->expectsJson()) {
            return EquipmentResource::make(
                $this->equipmentService->detail($equipment)
            )->additional([
                'message' => 'Equipment updated successfully.',
            ]);
        }

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('status', 'Equipment updated successfully.');
    }

    public function destroy(Request $request, Equipment $equipment): RedirectResponse|Response
    {
        $this->authorize('delete', $equipment);

        $workplace = $equipment->workplace;

        $this->equipmentService->archive($equipment);

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('workplaces.show', $workplace)
            ->with('status', 'Equipment archived successfully.');
    }
}
