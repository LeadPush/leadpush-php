<?php

declare(strict_types=1);

namespace Leadpush\SDK\Responses;

/**
 * Pagination metadata returned by the API.
 */
class PaginationMeta
{
    /**
     * Create pagination metadata.
     */
    public function __construct(
        private readonly int $currentPage,
        private readonly int $perPage,
        private readonly int $total,
        private readonly int $lastPage,
        private readonly bool $hasNext,
    ) {
    }

    /**
     * Create pagination metadata from an API response array.
     *
     * @param array{current_page?: int, per_page?: int, total?: int, last_page?: int, has_next?: bool} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['current_page'] ?? 1),
            (int) ($data['per_page'] ?? 0),
            (int) ($data['total'] ?? 0),
            (int) ($data['last_page'] ?? 1),
            (bool) ($data['has_next'] ?? false),
        );
    }

    /**
     * Current page number.
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Number of records returned per page.
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Total number of records matching the request.
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Last available page number.
     */
    public function lastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * Whether another page is available after the current page.
     */
    public function hasNext(): bool
    {
        return $this->hasNext;
    }

    /**
     * Return the metadata as a raw API-shaped array.
     *
     * @return array{current_page: int, per_page: int, total: int, last_page: int, has_next: bool}
     */
    public function toArray(): array
    {
        return [
            'current_page' => $this->currentPage,
            'per_page' => $this->perPage,
            'total' => $this->total,
            'last_page' => $this->lastPage,
            'has_next' => $this->hasNext,
        ];
    }
}
