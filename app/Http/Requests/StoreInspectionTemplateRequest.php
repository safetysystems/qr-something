<?php

namespace App\Http\Requests;

use App\Models\InspectionTemplate;
use App\Models\Workplace;
use Illuminate\Foundation\Http\FormRequest;

class StoreInspectionTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Workplace $workplace */
        $workplace = $this->route('workplace');

        return $this->user()?->can('create', [InspectionTemplate::class, $workplace]) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.label' => ['required', 'string', 'max:255'],
            'items.*.instructions' => ['nullable', 'string'],
            'items.*.is_required' => ['sometimes', 'boolean'],
        ];
    }
}
