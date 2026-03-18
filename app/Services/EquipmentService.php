<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class EquipmentService
{
    public function __construct(private readonly EquipmentQrCodeService $equipmentQrCodeService)
    {
    }

    public function paginateForWorkplace(Workplace $workplace, int $perPage = 10): LengthAwarePaginator
    {
        return $workplace->equipment()
            ->whereNull('archived_at')
            ->latest()
            ->paginate($perPage, ['*'], 'equipment_page')
            ->withQueryString();
    }

    public function detail(Equipment $equipment): Equipment
    {
        return $equipment->load(['workplace', 'creator']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Workplace $workplace, array $data, User $actor): Equipment
    {
        $equipment = $workplace->equipment()->create([
            ...$data,
            'uuid' => (string) Str::uuid(),
            'created_by' => $actor->id,
        ]);

        return $this->equipmentQrCodeService->ensureGenerated($equipment);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Equipment $equipment, array $data): Equipment
    {
        $equipment->update($data);

        return $this->equipmentQrCodeService->ensureGenerated($equipment->refresh());
    }

    public function archive(Equipment $equipment): void
    {
        $equipment->update([
            'archived_at' => now(),
        ]);
    }
}
