<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionResource extends JsonResource
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
            'status' => $this->status,
            'notes' => $this->notes,
            'inspected_at' => $this->inspected_at,
            'template' => $this->whenLoaded('template', fn () => [
                'uuid' => $this->template->uuid,
                'name' => $this->template->name,
            ]),
            'equipment' => $this->whenLoaded('equipment', fn () => [
                'uuid' => $this->equipment->uuid,
                'name' => $this->equipment->name,
            ]),
            'inspector' => $this->whenLoaded('inspector', fn () => [
                'id' => $this->inspector?->id,
                'name' => $this->inspector?->name,
            ]),
            'responses' => $this->whenLoaded('responses', fn () => $this->responses
                ->map(fn ($response) => InspectionResponseResource::make($response)->resolve($request))
                ->values()
                ->all()),
            'links' => [
                'show' => route('inspections.show', $this->resource),
            ],
        ];
    }
}
