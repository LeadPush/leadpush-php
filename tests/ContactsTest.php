<?php

declare(strict_types=1);

use Leadpush\SDK\Models\ContactModel;

use function Leadpush\SDK\Test\Support\contactData;
use function Leadpush\SDK\Test\Support\createClient;
use function Leadpush\SDK\Test\Support\createContactData;
use function Leadpush\SDK\Test\Support\expectedHeaderLines;
use function Leadpush\SDK\Test\Support\jsonResponse;
use function Leadpush\SDK\Test\Support\requestHeaders;
use function Leadpush\SDK\Test\Support\testBaseUrl;
use function Leadpush\SDK\Test\Support\unsubscribedContactData;
use function Leadpush\SDK\Test\Support\updatedContactData;
use function Leadpush\SDK\Test\Support\updateContactData;

it('gets a contact by uuid', function () {
    $response = jsonResponse([
        'data' => contactData(),
    ]);
    [$client] = createClient([$response]);

    $contact = $client->contacts()->get(contactData()['uuid']);

    expect($contact)->toBeInstanceOf(ContactModel::class)
        ->and($contact->uuid())->toBe(contactData()['uuid'])
        ->and($contact->attributes()['email'])->toBe(contactData()['attributes']['email'])
        ->and($response->getRequestMethod())->toBe('GET')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/69474ed13511f060ca09781a')
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines());
});

it('gets a contact by workspace identity value', function () {
    $response = jsonResponse([
        'data' => contactData(),
    ]);
    [$client] = createClient([$response]);

    $contact = $client->contacts()->get('contact@example.com');

    expect($contact)->toBeInstanceOf(ContactModel::class)
        ->and($contact->uuid())->toBe(contactData()['uuid'])
        ->and($contact->attributes()['email'])->toBe(contactData()['attributes']['email'])
        ->and($response->getRequestMethod())->toBe('GET')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/contact%40example.com')
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines());
});

it('creates a contact', function () {
    $response = jsonResponse([
        'data' => contactData(),
    ]);
    [$client] = createClient([$response]);

    $contact = $client->contacts()->create(createContactData());

    expect($contact)->toBeInstanceOf(ContactModel::class)
        ->and($contact->uuid())->toBe(contactData()['uuid'])
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts')
        ->and($response->getRequestOptions()['body'])->toBe(json_encode(createContactData(), JSON_THROW_ON_ERROR))
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines([
            'Content-Type' => 'application/json',
        ]));
});

it('updates a contact by uuid', function () {
    $response = jsonResponse([
        'data' => updatedContactData(),
    ]);
    [$client] = createClient([$response]);

    $contact = $client->contacts()->update(contactData()['uuid'], updateContactData());

    expect($contact)->toBeInstanceOf(ContactModel::class)
        ->and($contact->attributes()['first_name'])->toBe('Updated')
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/69474ed13511f060ca09781a')
        ->and($response->getRequestOptions()['body'])->toBe(json_encode(updateContactData(), JSON_THROW_ON_ERROR));
});

it('updates a contact by workspace identity value', function () {
    $response = jsonResponse([
        'data' => updatedContactData(),
    ]);
    [$client] = createClient([$response]);

    $contact = $client->contacts()->update('contact@example.com', updateContactData());

    expect($contact)->toBeInstanceOf(ContactModel::class)
        ->and($contact->uuid())->toBe(contactData()['uuid'])
        ->and($contact->attributes()['first_name'])->toBe('Updated')
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/contact%40example.com')
        ->and($response->getRequestOptions()['body'])->toBe(json_encode(updateContactData(), JSON_THROW_ON_ERROR));
});

it('subscribes a contact by workspace identity value', function () {
    $response = jsonResponse([
        'data' => contactData(),
    ]);
    [$client] = createClient([$response]);

    $contact = $client->contacts()->subscribe('contact@example.com');

    expect($contact)->toBeInstanceOf(ContactModel::class)
        ->and($contact->isSubscribed())->toBeTrue()
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/contact%40example.com/subscribe')
        ->and(requestHeaders($response))->not->toContain('Content-Type: application/json');
});

it('unsubscribes a contact by workspace identity value', function () {
    $response = jsonResponse([
        'data' => unsubscribedContactData(),
    ]);
    [$client] = createClient([$response]);

    $contact = $client->contacts()->unsubscribe('contact@example.com');

    expect($contact)->toBeInstanceOf(ContactModel::class)
        ->and($contact->isSubscribed())->toBeFalse()
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/contact%40example.com/unsubscribe')
        ->and(requestHeaders($response))->not->toContain('Content-Type: application/json');
});

it('updates an attached contact model', function () {
    $getResponse = jsonResponse([
        'data' => contactData(),
    ]);
    $updateResponse = jsonResponse([
        'data' => unsubscribedContactData(),
    ]);
    [$client] = createClient([$getResponse, $updateResponse]);

    $contact = $client->contacts()->get(contactData()['uuid']);
    $contact->setSubscribed(false);
    $updated = $contact->update();

    expect($updated)->toBe($contact)
        ->and($contact->isSubscribed())->toBeFalse()
        ->and($updateResponse->getRequestMethod())->toBe('POST')
        ->and($updateResponse->getRequestUrl())->toBe(testBaseUrl() . '/contacts/69474ed13511f060ca09781a')
        ->and($updateResponse->getRequestOptions()['body'])->toBe('{"subscribed":false}');
});

it('subscribes and unsubscribes an attached contact model', function () {
    $getResponse = jsonResponse([
        'data' => unsubscribedContactData(),
    ]);
    $subscribeResponse = jsonResponse([
        'data' => contactData(),
    ]);
    $unsubscribeResponse = jsonResponse([
        'data' => unsubscribedContactData(),
    ]);
    [$client] = createClient([$getResponse, $subscribeResponse, $unsubscribeResponse]);

    $contact = $client->contacts()->get(contactData()['uuid']);

    expect($contact->subscribe())->toBe($contact)
        ->and($contact->isSubscribed())->toBeTrue()
        ->and($subscribeResponse->getRequestUrl())->toBe(testBaseUrl() . '/contacts/69474ed13511f060ca09781a/subscribe')
        ->and($subscribeResponse->getRequestMethod())->toBe('POST')
        ->and(requestHeaders($subscribeResponse))->not->toContain('Content-Type: application/json')
        ->and($contact->unsubscribe())->toBe($contact)
        ->and($contact->isSubscribed())->toBeFalse()
        ->and($unsubscribeResponse->getRequestUrl())->toBe(testBaseUrl() . '/contacts/69474ed13511f060ca09781a/unsubscribe');
});

it('lists contacts from a paginated response', function () {
    $response = jsonResponse([
        'data' => [contactData()],
        'meta' => [
            'current_page' => 2,
            'per_page' => 1,
            'total' => 88,
            'last_page' => 88,
            'has_next' => true,
        ],
    ]);
    [$client] = createClient([$response]);

    $contacts = $client->contacts()->list([
        'page' => 2,
        'per_page' => 1,
    ]);

    expect($contacts->data()[0])->toBeInstanceOf(ContactModel::class)
        ->and($contacts->data()[0]->uuid())->toBe(contactData()['uuid'])
        ->and($contacts->meta()->total())->toBe(88)
        ->and($contacts->meta()->hasNext())->toBeTrue()
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts?page=2&per_page=1');
});
