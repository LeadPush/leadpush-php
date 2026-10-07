<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Leadpush;

final class Campaigns
{
    public function __construct(private readonly Leadpush $client)
    {
    }

    /**
     * @param array{search?: string, statuses?: array<int, string>, page?: int, perPage?: int} $params
     * @return array{data: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function list(array $params = []): array
    {
        return $this->client->get('campaigns', array_filter([
            'search' => $params['search'] ?? null,
            'statuses' => isset($params['statuses']) ? json_encode($params['statuses']) : null,
            'page' => $params['page'] ?? null,
            'per_page' => $params['perPage'] ?? null,
        ], static fn ($value) => $value !== null));
    }

    /** @return array<string, mixed> */
    public function get(string $campaignId): array
    {
        return $this->client->get(['campaigns', $campaignId])['data'];
    }

    /**
     * @param array{start: string, end: string, unit: 'day'} $range
     * @return array<string, mixed>
     */
    public function metrics(string $campaignId, array $range): array
    {
        return $this->client->get(['campaigns', $campaignId, 'metrics'], $range)['data'];
    }

    public function executions(string $campaignId): CampaignExecutions
    {
        return new CampaignExecutions($this->client, $campaignId);
    }
}
