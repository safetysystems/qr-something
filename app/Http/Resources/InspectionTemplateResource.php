<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'items_count' => $this->whenCounted('items'),
            'inspections_count' => $this->whenCounted('inspections'),
            'items' => $this->whenLoaded('items', fn () => $this->items
                ->map(fn ($item) => InspectionTemplateItemResource::make($item)->resolve($request))
                ->values()
                ->all()),
            'created_by' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
            ]),
            'archived_at' => $this->archived_at,
            'created_at' => $this->created_at,
            'links' => [
                'show' => route('inspection-templates.show', $this->resource),
                'edit' => route('inspection-templates.edit', $this->resource),
                'update' => route('inspection-templates.update', $this->resource),
                'destroy' => route('inspection-templates.destroy', $this->resource),
            ],
        ];
    }
}
