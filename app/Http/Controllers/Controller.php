<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\JsonResource;
abstract class Controller
{
    use AuthorizesRequests;

    /**
     * @param  class-string<JsonResource>  $resourceClass
     * @return array<int, array<string, mixed>>
     */
    protected function resourceCollectionData(iterable $items, string $resourceClass): array
    {
        return collect($items)
            ->map(fn (mixed $item): array => $resourceClass::make($item)->resolve(request()))
            ->values()
            ->all();
    }

    /**
     * @param  class-string<JsonResource>  $resourceClass
     * @return array<string, mixed>
     */
    protected function paginatedResourceData(LengthAwarePaginator $paginator, string $resourceClass): array
    {
        return [
            'data' => $this->resourceCollectionData($paginator->getCollection(), $resourceClass),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }
}
