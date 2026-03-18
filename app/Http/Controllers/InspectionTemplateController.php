<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectionTemplateRequest;
use App\Http\Requests\UpdateInspectionTemplateRequest;
use App\Http\Resources\InspectionTemplateResource;
use App\Http\Resources\WorkplaceResource;
use App\Models\InspectionTemplate;
use App\Models\Workplace;
use App\Services\InspectionTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class InspectionTemplateController extends Controller
{
    public function __construct(private readonly InspectionTemplateService $inspectionTemplateService)
    {
    }

    public function index(Request $request, Workplace $workplace): InertiaResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', [InspectionTemplate::class, $workplace]);

        $templates = $this->inspectionTemplateService->paginateForWorkplace($workplace);

        if ($request->expectsJson()) {
            return InspectionTemplateResource::collection($templates);
        }

        return Inertia::render('InspectionTemplates/Index', [
            'workplace' => WorkplaceResource::make($workplace)->resolve($request),
            'templates' => $this->paginatedResourceData($templates, InspectionTemplateResource::class),
            'links' => [
                'create' => route('workplaces.inspection-templates.create', $workplace),
            ],
        ]);
    }

    public function create(Request $request, Workplace $workplace): InertiaResponse
    {
        $this->authorize('create', [InspectionTemplate::class, $workplace]);

        return Inertia::render('InspectionTemplates/Create', [
            'workplace' => WorkplaceResource::make($workplace)->resolve($request),
            'links' => [
                'index' => route('workplaces.inspection-templates.index', $workplace),
                'store' => route('workplaces.inspection-templates.store', $workplace),
            ],
        ]);
    }

    public function store(StoreInspectionTemplateRequest $request, Workplace $workplace): JsonResponse|RedirectResponse
    {
        $template = $this->inspectionTemplateService->create($workplace, $request->validated(), $request->user());

        if ($request->expectsJson()) {
            return InspectionTemplateResource::make(
                $this->inspectionTemplateService->detail($template)
            )->additional([
                'message' => 'Inspection template created successfully.',
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        }

        return redirect()
            ->route('inspection-templates.show', $template)
            ->with('status', 'Inspection template created successfully.');
    }

    public function show(Request $request, InspectionTemplate $inspectionTemplate): InertiaResponse|InspectionTemplateResource
    {
        $this->authorize('view', $inspectionTemplate);

        $inspectionTemplate = $this->inspectionTemplateService->detail($inspectionTemplate);

        if ($request->expectsJson()) {
            return InspectionTemplateResource::make($inspectionTemplate);
        }

        return Inertia::render('InspectionTemplates/Show', [
            'template' => InspectionTemplateResource::make($inspectionTemplate)->resolve($request),
            'workplace' => WorkplaceResource::make($inspectionTemplate->workplace)->resolve($request),
        ]);
    }

    public function edit(Request $request, InspectionTemplate $inspectionTemplate): InertiaResponse
    {
        $this->authorize('update', $inspectionTemplate);

        $inspectionTemplate = $this->inspectionTemplateService->detail($inspectionTemplate);

        return Inertia::render('InspectionTemplates/Edit', [
            'template' => InspectionTemplateResource::make($inspectionTemplate)->resolve($request),
            'workplace' => WorkplaceResource::make($inspectionTemplate->workplace)->resolve($request),
        ]);
    }

    public function update(UpdateInspectionTemplateRequest $request, InspectionTemplate $inspectionTemplate): InspectionTemplateResource|RedirectResponse
    {
        $inspectionTemplate = $this->inspectionTemplateService->update($inspectionTemplate, $request->validated());

        if ($request->expectsJson()) {
            return InspectionTemplateResource::make(
                $this->inspectionTemplateService->detail($inspectionTemplate)
            )->additional([
                'message' => 'Inspection template updated successfully.',
            ]);
        }

        return redirect()
            ->route('inspection-templates.show', $inspectionTemplate)
            ->with('status', 'Inspection template updated successfully.');
    }

    public function destroy(Request $request, InspectionTemplate $inspectionTemplate): RedirectResponse|Response
    {
        $this->authorize('delete', $inspectionTemplate);

        $workplace = $inspectionTemplate->workplace;

        $this->inspectionTemplateService->archive($inspectionTemplate);

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('workplaces.inspection-templates.index', $workplace)
            ->with('status', 'Inspection template archived successfully.');
    }
}
