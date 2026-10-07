<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Leadpush;

final class Activity
{
    public function __construct(private readonly Leadpush $client)
    {
    }

    /**
     * @param array{eventTypes?: array<int, string>, search?: string, since?: string, page?: int, perPage?: int} $params
     * @return array{data: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function list(array $params = []): array
    {
        return $this->client->get('activity', array_filter([
            'event_types' => isset($params['eventTypes']) ? json_encode($params['eventTypes']) : null,
            'search' => $params['search'] ?? null,
            'since' => $params['since'] ?? null,
            'page' => $params['page'] ?? null,
            'per_page' => $params['perPage'] ?? null,
        ], static fn ($value) => $value !== null));
    }
}
