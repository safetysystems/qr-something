<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkplaceResource extends JsonResource
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
            'code' => $this->code,
            'primary_contact' => [
                'name' => $this->primary_contact_name,
                'email' => $this->primary_contact_email,
                'phone' => $this->primary_contact_phone,
            ],
            'address' => [
                'line_1' => $this->address_line_1,
                'line_2' => $this->address_line_2,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
            ],
            'equipment_count' => $this->whenCounted('equipment'),
            'archived_at' => $this->archived_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'show' => route('workplaces.show', $this->resource),
                'edit' => route('workplaces.edit', $this->resource),
                'update' => route('workplaces.update', $this->resource),
                'destroy' => route('workplaces.destroy', $this->resource),
                'inspection_templates' => route('workplaces.inspection-templates.index', $this->resource),
                'create_inspection_template' => route('workplaces.inspection-templates.create', $this->resource),
                'create_equipment' => route('workplaces.equipment.create', $this->resource),
                'store_equipment' => route('workplaces.equipment.store', $this->resource),
            ],
        ];
    }
}
