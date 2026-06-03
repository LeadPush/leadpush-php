<?php

declare(strict_types=1);

use Leadpush\SDK\Exceptions\UnsupportedEndpointError;
use Leadpush\SDK\Models\SuppressionModel;
use Symfony\Component\HttpClient\MockHttpClient;

use function Leadpush\SDK\Test\Support\createClient;
use function Leadpush\SDK\Test\Support\jsonResponse;
use function Leadpush\SDK\Test\Support\suppressionData;
use function Leadpush\SDK\Test\Support\suppressionFilters;
use function Leadpush\SDK\Test\Support\testBaseUrl;

it('gets a suppression by uuid', function () {
    $response = jsonResponse([
        'data' => suppressionData(),
    ]);
    [$client] = createClient([$response]);

    $suppression = $client->suppressions()->get(suppressionData()['uuid']);

    expect($suppression)->toBeInstanceOf(SuppressionModel::class)
        ->and($suppression->uuid())->toBe(suppressionData()['uuid'])
        ->and($suppression->email())->toBe(suppressionData()['email'])
        ->and($suppression->type())->toBe('manual')
        ->and($suppression->createdAt())->toEqual(new DateTimeImmutable(suppressionData()['created_at']))
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/suppressions/suppression-id');
});

it('lists suppressions with search and filters', function () {
    $response = jsonResponse([
        'data' => [suppressionData()],
        'meta' => [
            'current_page' => 1,
            'per_page' => 10,
            'total' => 1,
            'last_page' => 1,
            'has_next' => false,
        ],
    ]);
    [$client] = createClient([$response]);

    $suppressions = $client->suppressions()->list([
        'search' => 'blocked',
        'filters' => suppressionFilters(),
        'page' => 1,
        'per_page' => 10,
    ]);

    expect($suppressions->data()[0])->toBeInstanceOf(SuppressionModel::class)
        ->and($suppressions->data()[0]->email())->toBe(suppressionData()['email'])
        ->and($suppressions->meta()->total())->toBe(1)
        ->and($response->getRequestUrl())->toBe(
            testBaseUrl() . '/suppressions?search=blocked&filters=' . rawurlencode(json_encode(suppressionFilters(), JSON_THROW_ON_ERROR)) . '&page=1&per_page=10',
        );
});

it('throws when updating a suppression', function () {
    [$client, $http] = createClient();

    expect(fn () => $client->suppressions()->update(suppressionData()['uuid'], [
        'type' => 'manual',
    ]))->toThrow(UnsupportedEndpointError::class)
        ->and($http)->toBeInstanceOf(MockHttpClient::class)
        ->and($http->getRequestsCount())->toBe(0);
});
