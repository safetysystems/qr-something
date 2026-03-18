<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'response' => $this->response,
            'notes' => $this->notes,
            'template_item' => $this->whenLoaded('templateItem', fn () => [
                'id' => $this->templateItem->id,
                'label' => $this->templateItem->label,
                'instructions' => $this->templateItem->instructions,
                'is_required' => $this->templateItem->is_required,
            ]),
        ];
    }
}
