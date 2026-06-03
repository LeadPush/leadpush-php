<?php

declare(strict_types=1);

namespace Leadpush\SDK\Responses;

use Leadpush\SDK\Model;

/**
 * Paginated response returned by list operations.
 */
class PaginatedResponse
{
    /**
     * @param array<int, Model> $data Models returned for the current page.
     */
    public function __construct(
        private readonly array $data,
        private readonly PaginationMeta $meta,
    ) {
    }

    /**
     * Models returned for the current page.
     *
     * @return array<int, Model>
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Pagination metadata returned by the API.
     */
    public function meta(): PaginationMeta
    {
        return $this->meta;
    }

    /**
     * Return the response as raw API-shaped arrays.
     *
     * @return array{data: array<int, array<string, mixed>>, meta: array<string, int|bool>}
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(
                fn (mixed $item): mixed => $item instanceof Model ? $item->toArray() : $item,
                $this->data,
            ),
            'meta' => $this->meta->toArray(),
        ];
    }
}
