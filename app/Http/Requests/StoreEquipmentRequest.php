<?php

namespace App\Http\Requests;

use App\Models\Equipment;
use App\Models\Workplace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $workplace = $this->resolveWorkplace();

        if (! $workplace) {
            return false;
        }

        return $this->user()?->can('create', [Equipment::class, $workplace]) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workplace = $this->resolveWorkplace();

        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'asset_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('equipment', 'asset_code')->where(
                    fn ($query) => $query
                        ->where('workplace_id', $workplace->id)
                        ->whereNull('archived_at')
                ),
            ],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'location_label' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function resolveWorkplace(): ?Workplace
    {
        $workplace = $this->route('workplace');

        if ($workplace instanceof Workplace) {
            return $workplace;
        }

        return $this->user()?->currentWorkplace();
    }
}
