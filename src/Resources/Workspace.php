<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Leadpush;

final class Workspace
{
    public function __construct(private readonly Leadpush $client)
    {
    }

    /** @return array<string, mixed> */
    public function get(): array
    {
        return $this->client->get('workspace')['data'];
    }
}
