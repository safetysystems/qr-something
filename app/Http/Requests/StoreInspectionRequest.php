<?php

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\InspectionTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreInspectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Equipment $equipment */
        $equipment = $this->route('equipment');

        return $this->user()?->can('create', [Inspection::class, $equipment]) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'inspection_template_uuid' => ['required', 'string', 'exists:inspection_templates,uuid'],
            'inspected_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'responses' => ['required', 'array', 'min:1'],
            'responses.*.inspection_template_item_id' => ['required', 'integer', 'exists:inspection_template_items,id'],
            'responses.*.response' => ['required', 'string', Rule::in(['pass', 'fail', 'not_applicable'])],
            'responses.*.notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Equipment $equipment */
            $equipment = $this->route('equipment');

            $template = InspectionTemplate::query()
                ->where('uuid', $this->input('inspection_template_uuid'))
                ->where('workplace_id', $equipment->workplace_id)
                ->whereNull('archived_at')
                ->with('items')
                ->first();

            if (! $template) {
                $validator->errors()->add('inspection_template_uuid', 'The selected inspection template is invalid for this equipment.');

                return;
            }

            $submittedIds = collect(Arr::wrap($this->input('responses')))
                ->pluck('inspection_template_item_id')
                ->map(fn ($id) => (int) $id)
                ->values();

            $templateIds = $template->items->pluck('id')->values();

            if ($submittedIds->count() !== $templateIds->count() || $submittedIds->sort()->values()->all() !== $templateIds->sort()->values()->all()) {
                $validator->errors()->add('responses', 'Responses must match the selected template checklist items exactly.');
            }
        });
    }
}
