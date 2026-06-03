<?php

declare(strict_types=1);

namespace Leadpush\SDK;

use Leadpush\SDK\Responses\PaginatedResponse;
use Leadpush\SDK\Responses\PaginationMeta;

/**
 * Base class for resources that support paginated listing.
 *
 * @internal
 */
abstract class ListableResource extends Resource
{
    /**
     * List one page of resources.
     *
     * @param array<string, mixed> $params Optional pagination, search, filter, or resource-specific query parameters.
     */
    public function list(array $params = []): PaginatedResponse
    {
        $payload = $this->getResource(null, $params);
        $models = array_map(fn (array $item): Model => $this->makeModel($item), $payload['data'] ?? []);

        return new PaginatedResponse($models, PaginationMeta::fromArray($payload['meta'] ?? []));
    }

    /**
     * Iterate all resources across all available pages.
     *
     * @param array<string, mixed> $params Optional pagination, search, filter, or resource-specific query parameters.
     * @return \Generator<int, Model>
     */
    public function listAll(array $params = []): \Generator
    {
        foreach ($this->cursor($params) as $page) {
            foreach ($page->data() as $item) {
                yield $item;
            }
        }
    }

    /**
     * Iterate paginated responses across all available pages.
     *
     * @param array<string, mixed> $params Optional pagination, search, filter, or resource-specific query parameters.
     * @return \Generator<int, PaginatedResponse>
     */
    public function cursor(array $params = []): \Generator
    {
        $page = $params['page'] ?? 1;

        while (true) {
            $result = $this->list(array_replace($params, [
                'page' => $page,
            ]));

            yield $result;

            if (! $result->meta()->hasNext()) {
                return;
            }

            $page = $result->meta()->currentPage() + 1;
        }
    }
}
