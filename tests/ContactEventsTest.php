<?php

declare(strict_types=1);

use Leadpush\SDK\Models\ContactEventModel;

use function Leadpush\SDK\Test\Support\contactData;
use function Leadpush\SDK\Test\Support\contactEventData;
use function Leadpush\SDK\Test\Support\createClient;
use function Leadpush\SDK\Test\Support\createContactEventData;
use function Leadpush\SDK\Test\Support\emptyResponse;
use function Leadpush\SDK\Test\Support\expectedHeaderLines;
use function Leadpush\SDK\Test\Support\jsonResponse;
use function Leadpush\SDK\Test\Support\requestHeaders;
use function Leadpush\SDK\Test\Support\testBaseUrl;

it('lists contact events from the contacts resource', function () {
    $response = jsonResponse([
        'data' => [contactEventData()],
        'meta' => [
            'current_page' => 2,
            'per_page' => 1,
            'total' => 3,
            'last_page' => 3,
            'has_next' => true,
        ],
    ]);
    [$client] = createClient([$response]);

    $events = $client->contacts()->events(contactData()['uuid'])->list([
        'page' => 2,
        'per_page' => 1,
        'search' => 'purchase',
    ]);

    expect($events->data()[0])->toBeInstanceOf(ContactEventModel::class)
        ->and($events->data()[0]->uuid())->toBe(contactEventData()['uuid'])
        ->and($events->data()[0]->eventName())->toBe(contactEventData()['event_name'])
        ->and($events->data()[0]->type())->toBe(contactEventData()['event_name'])
        ->and($events->data()[0]->attributes())->toBe(contactEventData()['attributes'])
        ->and($events->data()[0]->createdAt())->toEqual(new DateTimeImmutable(contactEventData()['created_at']))
        ->and($events->data()[0]->toArray())->toBe(contactEventData())
        ->and($events->meta()->total())->toBe(3)
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/' . contactData()['uuid'] . '/events?page=2&per_page=1&search=purchase');
});

it('lists contact events by workspace identity value', function () {
    $response = jsonResponse([
        'data' => [contactEventData()],
        'meta' => [
            'current_page' => 1,
            'per_page' => 10,
            'total' => 1,
            'last_page' => 1,
            'has_next' => false,
        ],
    ]);
    [$client] = createClient([$response]);

    $events = $client->contacts()->events('contact@example.com')->list();

    expect($events->data()[0])->toBeInstanceOf(ContactEventModel::class)
        ->and($events->data()[0]->uuid())->toBe(contactEventData()['uuid'])
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/contact%40example.com/events');
});

it('lists contact events from an attached contact model', function () {
    $contactResponse = jsonResponse([
        'data' => contactData(),
    ]);
    $eventsResponse = jsonResponse([
        'data' => [contactEventData()],
        'meta' => [
            'current_page' => 1,
            'per_page' => 10,
            'total' => 1,
            'last_page' => 1,
            'has_next' => false,
        ],
    ]);
    [$client] = createClient([$contactResponse, $eventsResponse]);

    $contact = $client->contacts()->get(contactData()['uuid']);
    $events = $contact->events()->list();

    expect($events->data()[0])->toBeInstanceOf(ContactEventModel::class)
        ->and($eventsResponse->getRequestUrl())->toBe(testBaseUrl() . '/contacts/' . contactData()['uuid'] . '/events');
});

it('creates contact events', function () {
    $response = emptyResponse();
    [$client] = createClient([$response]);

    $result = $client->contacts()->events(contactData()['uuid'])->create(createContactEventData());

    expect($result)->toBeNull()
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/' . contactData()['uuid'] . '/events')
        ->and($response->getRequestOptions()['body'])->toBe(json_encode([
            'event_name' => createContactEventData()['event_name'],
            'attributes' => json_encode(createContactEventData()['attributes'], JSON_THROW_ON_ERROR),
        ], JSON_THROW_ON_ERROR))
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines([
            'Content-Type' => 'application/json',
        ]));
});

it('creates contact events by workspace identity value', function () {
    $response = emptyResponse();
    [$client] = createClient([$response]);

    $result = $client->contacts()->events('contact@example.com')->create(createContactEventData());

    expect($result)->toBeNull()
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/contact%40example.com/events')
        ->and($response->getRequestOptions()['body'])->toBe(json_encode([
            'event_name' => createContactEventData()['event_name'],
            'attributes' => json_encode(createContactEventData()['attributes'], JSON_THROW_ON_ERROR),
        ], JSON_THROW_ON_ERROR))
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines([
            'Content-Type' => 'application/json',
        ]));
});

it('creates contact events without attributes', function () {
    $response = emptyResponse();
    [$client] = createClient([$response]);

    $result = $client->contacts()->events(contactData()['uuid'])->create([
        'event_name' => 'test',
    ]);

    expect($result)->toBeNull()
        ->and($response->getRequestOptions()['body'])->toBe('{"event_name":"test"}');
});
