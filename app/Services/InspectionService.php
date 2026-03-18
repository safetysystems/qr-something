<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\InspectionTemplate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InspectionService
{
    public function detail(Inspection $inspection): Inspection
    {
        return $inspection->load([
            'equipment',
            'inspector',
            'template',
            'responses.templateItem',
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inspection>
     */
    public function recentForEquipment(Equipment $equipment, int $limit = 5)
    {
        return $equipment->inspections()
            ->with(['template', 'inspector'])
            ->latest('inspected_at')
            ->take($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Equipment $equipment, InspectionTemplate $template, array $data, User $actor): Inspection
    {
        return DB::transaction(function () use ($equipment, $template, $data, $actor): Inspection {
            $responses = $data['responses'];

            $status = collect($responses)->contains(fn (array $response): bool => $response['response'] === 'fail')
                ? 'failed'
                : 'passed';

            $inspection = Inspection::query()->create([
                'uuid' => (string) Str::uuid(),
                'workplace_id' => $equipment->workplace_id,
                'equipment_id' => $equipment->id,
                'inspection_template_id' => $template->id,
                'user_id' => $actor->id,
                'status' => $status,
                'notes' => $data['notes'] ?? null,
                'inspected_at' => $data['inspected_at'],
            ]);

            foreach ($responses as $response) {
                $inspection->responses()->create([
                    'inspection_template_item_id' => $response['inspection_template_item_id'],
                    'response' => $response['response'],
                    'notes' => $response['notes'] ?? null,
                ]);
            }

            return $inspection->refresh();
        });
    }
}
