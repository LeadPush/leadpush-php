<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Leadpush;

final class Metrics
{
    public function __construct(private readonly Leadpush $client)
    {
    }

    /**
     * @param array{start: string, end: string, unit: 'day'} $range
     * @return array<string, mixed>
     */
    public function delivery(array $range): array
    {
        return $this->client->get(['metrics', 'delivery'], $range)['data'];
    }
}
