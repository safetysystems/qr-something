<?php

namespace App\Services;

use App\Models\InspectionTemplate;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InspectionTemplateService
{
    public function paginateForWorkplace(Workplace $workplace, int $perPage = 10): LengthAwarePaginator
    {
        return $workplace->inspectionTemplates()
            ->whereNull('archived_at')
            ->withCount(['items', 'inspections'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function availableForWorkplace(Workplace $workplace)
    {
        return $workplace->inspectionTemplates()
            ->whereNull('archived_at')
            ->with('items')
            ->withCount(['items', 'inspections'])
            ->latest()
            ->get();
    }

    public function recentForWorkplace(Workplace $workplace, int $limit = 4)
    {
        return $workplace->inspectionTemplates()
            ->whereNull('archived_at')
            ->withCount(['items', 'inspections'])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function detail(InspectionTemplate $template): InspectionTemplate
    {
        return $template
            ->load(['workplace', 'creator', 'items'])
            ->loadCount(['items', 'inspections']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Workplace $workplace, array $data, User $actor): InspectionTemplate
    {
        return DB::transaction(function () use ($workplace, $data, $actor): InspectionTemplate {
            $template = $workplace->inspectionTemplates()->create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'created_by' => $actor->id,
            ]);

            $this->syncItems($template, $data['items']);

            return $template->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(InspectionTemplate $template, array $data): InspectionTemplate
    {
        if ($template->inspections()->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Templates with submitted inspections cannot be edited. Create a new template version instead.',
            ]);
        }

        return DB::transaction(function () use ($template, $data): InspectionTemplate {
            $template->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            $template->items()->delete();
            $this->syncItems($template, $data['items']);

            return $template->refresh();
        });
    }

    public function archive(InspectionTemplate $template): void
    {
        $template->update([
            'archived_at' => now(),
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function syncItems(InspectionTemplate $template, array $items): void
    {
        foreach (array_values($items) as $index => $item) {
            $template->items()->create([
                'label' => $item['label'],
                'instructions' => $item['instructions'] ?? null,
                'sort_order' => $index + 1,
                'is_required' => $item['is_required'] ?? true,
            ]);
        }
    }
}
