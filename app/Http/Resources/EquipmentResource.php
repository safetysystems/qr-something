<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
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
            'type' => $this->type,
            'asset_code' => $this->asset_code,
            'serial_number' => $this->serial_number,
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'location_label' => $this->location_label,
            'notes' => $this->notes,
            'workplace' => $this->whenLoaded('workplace', fn () => [
                'uuid' => $this->workplace->uuid,
                'name' => $this->workplace->name,
            ]),
            'created_by' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
            ]),
            'archived_at' => $this->archived_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'qr_code_generated_at' => $this->qr_code_generated_at,
            'qr_url' => route('equipment.show', $this->resource),
            'qr_code' => [
                'available' => (bool) $this->qr_code_path,
                'generated_at' => $this->qr_code_generated_at,
                'links' => [
                    'image' => route('equipment.qr-code.show', $this->resource),
                    'download' => route('equipment.qr-code.download', $this->resource),
                    'regenerate' => route('equipment.qr-code.regenerate', $this->resource),
                ],
            ],
            'links' => [
                'show' => route('equipment.show', $this->resource),
                'client_show' => route('client.equipment.show', $this->resource),
                'edit' => route('equipment.edit', $this->resource),
                'client_edit' => route('client.equipment.edit', $this->resource),
                'update' => route('equipment.update', $this->resource),
                'client_update' => route('client.equipment.update', $this->resource),
                'destroy' => route('equipment.destroy', $this->resource),
                'client_destroy' => route('client.equipment.destroy', $this->resource),
                'create_inspection' => route('equipment.inspections.create', $this->resource),
                'store_inspection' => route('equipment.inspections.store', $this->resource),
                'workplace' => $this->relationLoaded('workplace') && $this->workplace
                    ? route('workplaces.show', $this->workplace)
                    : null,
            ],
        ];
    }
}
