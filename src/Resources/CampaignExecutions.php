<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Leadpush;

final class CampaignExecutions
{
    public function __construct(
        private readonly Leadpush $client,
        private readonly string $campaignId,
    ) {
    }

    /** @return array<string, mixed> */
    public function get(string $executionId, ?int $stepLimit = null): array
    {
        return $this->client->get(
            ['campaigns', $this->campaignId, 'executions', $executionId],
            array_filter(['step_limit' => $stepLimit], static fn ($value) => $value !== null),
        )['data'];
    }
}
