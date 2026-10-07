<?php

declare(strict_types=1);

use function Leadpush\SDK\Test\Support\createClient;
use function Leadpush\SDK\Test\Support\jsonResponse;
use function Leadpush\SDK\Test\Support\testBaseUrl;

it('exposes resource-oriented read endpoints', function () {
    $responses = array_map(jsonResponse(...), [
        ['data' => ['workspace' => ['uuid' => 'workspace-1']]],
        ['data' => [['uuid' => 'campaign-1']], 'meta' => []],
        ['data' => ['uuid' => 'campaign-1']],
        ['data' => ['range' => [], 'summary' => [], 'series' => []]],
        ['data' => ['range' => [], 'summary' => [], 'series' => []]],
        ['data' => [], 'meta' => []],
        ['data' => ['execution' => ['uuid' => 'execution-1']]],
    ]);
    [$client] = createClient($responses);
    $range = ['start' => '2026-10-01T00:00:00Z', 'end' => '2026-10-06T00:00:00Z', 'unit' => 'day'];

    $client->workspace()->get();
    $client->campaigns()->list(['statuses' => ['draft'], 'perPage' => 20]);
    $client->campaigns()->get('campaign-1');
    $client->campaigns()->metrics('campaign-1', $range);
    $client->metrics()->delivery($range);
    $client->activity()->list(['eventTypes' => ['contact_created']]);
    $client->campaigns()->executions('campaign-1')->get('execution-1', 10);

    expect($responses[0]->getRequestUrl())->toBe(testBaseUrl() . '/workspace')
        ->and($responses[1]->getRequestUrl())->toContain('/campaigns?statuses=%5B%22draft%22%5D&per_page=20')
        ->and($responses[2]->getRequestUrl())->toBe(testBaseUrl() . '/campaigns/campaign-1')
        ->and($responses[3]->getRequestUrl())->toContain('/campaigns/campaign-1/metrics?')
        ->and($responses[4]->getRequestUrl())->toContain('/metrics/delivery?')
        ->and($responses[5]->getRequestUrl())->toContain('/activity?event_types=%5B%22contact_created%22%5D')
        ->and($responses[6]->getRequestUrl())->toContain('/campaigns/campaign-1/executions/execution-1?step_limit=10');
});
