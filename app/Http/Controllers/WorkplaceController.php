<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkplaceRequest;
use App\Http\Requests\UpdateWorkplaceRequest;
use App\Http\Resources\InspectionTemplateResource;
use App\Http\Resources\WorkplaceResource;
use App\Models\Workplace;
use App\Services\EquipmentService;
use App\Services\InspectionTemplateService;
use App\Services\WorkplaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class WorkplaceController extends Controller
{
    public function __construct(
        private readonly WorkplaceService $workplaceService,
        private readonly EquipmentService $equipmentService,
        private readonly InspectionTemplateService $inspectionTemplateService,
    ) {
    }

    public function index(Request $request): InertiaResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', Workplace::class);

        $workplaces = $this->workplaceService->paginateOverview();

        if ($request->expectsJson()) {
            return WorkplaceResource::collection($workplaces);
        }

        return Inertia::render('Workplaces/Index', [
            'workplaces' => $this->paginatedResourceData($workplaces, WorkplaceResource::class),
            'links' => [
                'create' => route('workplaces.create'),
            ],
        ]);
    }

    public function create(): InertiaResponse
    {
        $this->authorize('create', Workplace::class);

        return Inertia::render('Workplaces/Create', [
            'links' => [
                'index' => route('workplaces.index'),
                'store' => route('workplaces.store'),
            ],
        ]);
    }

    public function store(StoreWorkplaceRequest $request): JsonResponse|RedirectResponse
    {
        $workplace = $this->workplaceService->create($request->validated());

        if ($request->expectsJson()) {
            return WorkplaceResource::make(
                $this->workplaceService->detail($workplace)
            )->additional([
                'message' => 'Workplace created successfully.',
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        }

        return redirect()
            ->route('workplaces.show', $workplace)
            ->with('status', 'Workplace created successfully.');
    }

    public function show(Request $request, Workplace $workplace): InertiaResponse|WorkplaceResource
    {
        $this->authorize('view', $workplace);

        $workplace = $this->workplaceService->detail($workplace);
        $equipment = $this->equipmentService->paginateForWorkplace($workplace);
        $inspectionTemplates = $this->inspectionTemplateService->recentForWorkplace($workplace);

        if ($request->expectsJson()) {
            return WorkplaceResource::make($workplace);
        }

        return Inertia::render('Workplaces/Show', [
            'workplace' => WorkplaceResource::make($workplace)->resolve($request),
            'equipment' => $this->paginatedResourceData($equipment, \App\Http\Resources\EquipmentResource::class),
            'inspectionTemplates' => $this->resourceCollectionData($inspectionTemplates, InspectionTemplateResource::class),
            'templateLinks' => [
                'index' => route('workplaces.inspection-templates.index', $workplace),
                'create' => route('workplaces.inspection-templates.create', $workplace),
            ],
        ]);
    }

    public function edit(Request $request, Workplace $workplace): InertiaResponse
    {
        $this->authorize('update', $workplace);

        return Inertia::render('Workplaces/Edit', [
            'workplace' => WorkplaceResource::make($workplace)->resolve($request),
        ]);
    }

    public function update(UpdateWorkplaceRequest $request, Workplace $workplace): WorkplaceResource|RedirectResponse
    {
        $workplace = $this->workplaceService->update($workplace, $request->validated());

        if ($request->expectsJson()) {
            return WorkplaceResource::make(
                $this->workplaceService->detail($workplace)
            )->additional([
                'message' => 'Workplace updated successfully.',
            ]);
        }

        return redirect()
            ->route('workplaces.show', $workplace)
            ->with('status', 'Workplace updated successfully.');
    }

    public function destroy(Request $request, Workplace $workplace): RedirectResponse|Response
    {
        $this->authorize('delete', $workplace);

        $this->workplaceService->archive($workplace);

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('workplaces.index')
            ->with('status', 'Workplace archived successfully.');
    }
}
