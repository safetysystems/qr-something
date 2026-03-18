<?php

namespace App\Services;

use App\Models\Workplace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkplaceService
{
    public function paginateOverview(int $perPage = 10): LengthAwarePaginator
    {
        return Workplace::query()
            ->whereNull('archived_at')
            ->withCount(['equipment' => fn ($query) => $query->whereNull('archived_at')])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function detail(Workplace $workplace): Workplace
    {
        return $workplace->loadCount([
            'equipment' => fn ($query) => $query->whereNull('archived_at'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Workplace
    {
        return Workplace::query()->create([
            ...$data,
            'uuid' => (string) Str::uuid(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Workplace $workplace, array $data): Workplace
    {
        $workplace->update($data);

        return $workplace->refresh();
    }

    public function archive(Workplace $workplace): void
    {
        $archivedAt = now();

        DB::transaction(function () use ($workplace, $archivedAt): void {
            $workplace->update(['archived_at' => $archivedAt]);

            $workplace->equipment()
                ->whereNull('archived_at')
                ->update(['archived_at' => $archivedAt]);
        });
    }
}
