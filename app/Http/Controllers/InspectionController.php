<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectionRequest;
use App\Http\Resources\EquipmentResource;
use App\Http\Resources\InspectionResource;
use App\Http\Resources\InspectionTemplateResource;
use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\InspectionTemplate;
use App\Services\InspectionService;
use App\Services\InspectionTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class InspectionController extends Controller
{
    public function __construct(
        private readonly InspectionService $inspectionService,
        private readonly InspectionTemplateService $inspectionTemplateService,
    ) {
    }

    public function create(Request $request, Equipment $equipment): InertiaResponse
    {
        $this->authorize('create', [Inspection::class, $equipment]);

        $equipment = $equipment->load('workplace', 'creator');
        $templates = $this->inspectionTemplateService->availableForWorkplace($equipment->workplace);

        return Inertia::render('Inspections/Create', [
            'equipment' => EquipmentResource::make($equipment)->resolve($request),
            'templates' => $this->resourceCollectionData($templates, InspectionTemplateResource::class),
        ]);
    }

    public function store(StoreInspectionRequest $request, Equipment $equipment): JsonResponse|RedirectResponse
    {
        $template = InspectionTemplate::query()
            ->where('uuid', $request->validated('inspection_template_uuid'))
            ->where('workplace_id', $equipment->workplace_id)
            ->firstOrFail();

        $inspection = $this->inspectionService->create(
            $equipment,
            $template,
            $request->validated(),
            $request->user(),
        );

        if ($request->expectsJson()) {
            return InspectionResource::make(
                $this->inspectionService->detail($inspection)
            )->additional([
                'message' => 'Inspection submitted successfully.',
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        }

        return redirect()
            ->route('inspections.show', $inspection)
            ->with('status', 'Inspection submitted successfully.');
    }

    public function show(Request $request, Inspection $inspection): InertiaResponse|InspectionResource
    {
        $this->authorize('view', $inspection);

        $inspection = $this->inspectionService->detail($inspection);

        if ($request->expectsJson()) {
            return InspectionResource::make($inspection);
        }

        return Inertia::render('Inspections/Show', [
            'inspection' => InspectionResource::make($inspection)->resolve($request),
            'equipment' => EquipmentResource::make($inspection->equipment)->resolve($request),
        ]);
    }
}
