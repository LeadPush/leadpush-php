<?php

declare(strict_types=1);

use Leadpush\SDK\Models\DomainAddressModel;
use Leadpush\SDK\Models\DomainModel;

use function Leadpush\SDK\Test\Support\createClient;
use function Leadpush\SDK\Test\Support\createDomainAddressData;
use function Leadpush\SDK\Test\Support\createDomainData;
use function Leadpush\SDK\Test\Support\domainAddressData;
use function Leadpush\SDK\Test\Support\domainData;
use function Leadpush\SDK\Test\Support\emptyResponse;
use function Leadpush\SDK\Test\Support\expectedHeaderLines;
use function Leadpush\SDK\Test\Support\jsonResponse;
use function Leadpush\SDK\Test\Support\requestHeaders;
use function Leadpush\SDK\Test\Support\testBaseUrl;
use function Leadpush\SDK\Test\Support\verifiedDomainData;

it('lists domains with search and pagination', function () {
    $response = jsonResponse([
        'data' => [domainData()],
        'meta' => [
            'current_page' => 2,
            'per_page' => 1,
            'total' => 3,
            'last_page' => 3,
            'has_next' => true,
        ],
    ]);
    [$client] = createClient([$response]);

    $domains = $client->domains()->list([
        'page' => 2,
        'per_page' => 1,
        'search' => 'example',
    ]);

    expect($domains->data()[0])->toBeInstanceOf(DomainModel::class)
        ->and($domains->data()[0]->uuid())->toBe(domainData()['uuid'])
        ->and($domains->data()[0]->name())->toBe(domainData()['name'])
        ->and($domains->data()[0]->domain())->toBe(domainData()['domain'])
        ->and($domains->data()[0]->isVerified())->toBeFalse()
        ->and($domains->data()[0]->provider())->toBe('leadpush')
        ->and($domains->data()[0]->status())->toBe('pending')
        ->and($domains->data()[0]->verification())->toBe('pending')
        ->and($domains->data()[0]->mailFromDomain())->toBe(domainData()['mail_from_domain'])
        ->and($domains->data()[0]->isMailFromVerified())->toBeFalse()
        ->and($domains->data()[0]->dns())->toBe(domainData()['dns'])
        ->and($domains->data()[0]->createdAt())->toEqual(new DateTimeImmutable(domainData()['created_at']))
        ->and($domains->data()[0]->updatedAt())->toEqual(new DateTimeImmutable(domainData()['updated_at']))
        ->and($domains->data()[0]->toArray())->toBe(domainData())
        ->and($domains->meta()->total())->toBe(3)
        ->and($domains->meta()->hasNext())->toBeTrue()
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/domains?page=2&per_page=1&search=example')
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines());
});

it('gets a domain by uuid', function () {
    $response = jsonResponse([
        'data' => domainData(),
    ]);
    [$client] = createClient([$response]);

    $domain = $client->domains()->get(domainData()['uuid']);

    expect($domain)->toBeInstanceOf(DomainModel::class)
        ->and($domain->uuid())->toBe(domainData()['uuid'])
        ->and($domain->name())->toBe(domainData()['name'])
        ->and($response->getRequestMethod())->toBe('GET')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid');
});

it('creates a domain', function () {
    $response = jsonResponse([
        'data' => domainData(),
    ]);
    [$client] = createClient([$response]);

    $domain = $client->domains()->create(createDomainData());

    expect($domain)->toBeInstanceOf(DomainModel::class)
        ->and($domain->uuid())->toBe(domainData()['uuid'])
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/domains')
        ->and($response->getRequestOptions()['body'])->toBe(json_encode(createDomainData(), JSON_THROW_ON_ERROR))
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines([
            'Content-Type' => 'application/json',
        ]));
});

it('deletes a domain by uuid', function () {
    $response = emptyResponse();
    [$client] = createClient([$response]);

    $result = $client->domains()->delete(domainData()['uuid']);

    expect($result)->toBeNull()
        ->and($response->getRequestMethod())->toBe('DELETE')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid')
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines());
});

it('refreshes domain verification by uuid', function () {
    $response = jsonResponse([
        'data' => verifiedDomainData(),
    ]);
    [$client] = createClient([$response]);

    $domain = $client->domains()->verify(domainData()['uuid']);

    expect($domain)->toBeInstanceOf(DomainModel::class)
        ->and($domain->isVerified())->toBeTrue()
        ->and($domain->verification())->toBe('completed')
        ->and($domain->isMailFromVerified())->toBeTrue()
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid/verification')
        ->and(requestHeaders($response))->not->toContain('Content-Type: application/json');
});

it('refreshes verification from an attached domain model', function () {
    $getResponse = jsonResponse([
        'data' => domainData(),
    ]);
    $verifyResponse = jsonResponse([
        'data' => verifiedDomainData(),
    ]);
    [$client] = createClient([$getResponse, $verifyResponse]);

    $domain = $client->domains()->get(domainData()['uuid']);

    expect($domain->isVerified())->toBeFalse();

    $verified = $domain->verify();

    expect($verified)->toBe($domain)
        ->and($domain->isVerified())->toBeTrue()
        ->and($verifyResponse->getRequestMethod())->toBe('POST')
        ->and($verifyResponse->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid/verification');
});

it('deletes an attached domain model', function () {
    $getResponse = jsonResponse([
        'data' => domainData(),
    ]);
    $deleteResponse = emptyResponse();
    [$client] = createClient([$getResponse, $deleteResponse]);

    $domain = $client->domains()->get(domainData()['uuid']);
    $result = $domain->delete();

    expect($result)->toBeNull()
        ->and($deleteResponse->getRequestMethod())->toBe('DELETE')
        ->and($deleteResponse->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid');
});

it('lists domain addresses from the domains resource', function () {
    $response = jsonResponse([
        'data' => [domainAddressData()],
        'meta' => [
            'current_page' => 1,
            'per_page' => 10,
            'total' => 1,
            'last_page' => 1,
            'has_next' => false,
        ],
    ]);
    [$client] = createClient([$response]);

    $addresses = $client->domains()->addresses(domainData()['uuid'])->list([
        'page' => 1,
        'per_page' => 10,
    ]);

    expect($addresses->data()[0])->toBeInstanceOf(DomainAddressModel::class)
        ->and($addresses->data()[0]->uuid())->toBe(domainAddressData()['uuid'])
        ->and($addresses->data()[0]->domainUuid())->toBe(domainData()['uuid'])
        ->and($addresses->data()[0]->address())->toBe(domainAddressData()['address'])
        ->and($addresses->data()[0]->fullAddress())->toBe(domainAddressData()['full_address'])
        ->and($addresses->data()[0]->provider())->toBe('leadpush')
        ->and($addresses->data()[0]->displayName())->toBe(domainAddressData()['display_name'])
        ->and($addresses->data()[0]->verification())->toBe('completed')
        ->and($addresses->data()[0]->createdAt())->toEqual(new DateTimeImmutable(domainAddressData()['created_at']))
        ->and($addresses->data()[0]->updatedAt())->toEqual(new DateTimeImmutable(domainAddressData()['updated_at']))
        ->and($addresses->data()[0]->toArray())->toBe(domainAddressData())
        ->and($addresses->meta()->total())->toBe(1)
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid/addresses?page=1&per_page=10');
});

it('lists domain addresses from an attached domain model', function () {
    $domainResponse = jsonResponse([
        'data' => domainData(),
    ]);
    $addressesResponse = jsonResponse([
        'data' => [domainAddressData()],
        'meta' => [
            'current_page' => 1,
            'per_page' => 10,
            'total' => 1,
            'last_page' => 1,
            'has_next' => false,
        ],
    ]);
    [$client] = createClient([$domainResponse, $addressesResponse]);

    $domain = $client->domains()->get(domainData()['uuid']);
    $addresses = $domain->addresses()->list();

    expect($addresses->data()[0])->toBeInstanceOf(DomainAddressModel::class)
        ->and($addresses->data()[0]->uuid())->toBe(domainAddressData()['uuid'])
        ->and($addressesResponse->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid/addresses');
});

it('gets a nested domain address', function () {
    $response = jsonResponse([
        'data' => domainAddressData(),
    ]);
    [$client] = createClient([$response]);

    $address = $client->domains()->addresses(domainData()['uuid'])->get(domainAddressData()['uuid']);

    expect($address)->toBeInstanceOf(DomainAddressModel::class)
        ->and($address->uuid())->toBe(domainAddressData()['uuid'])
        ->and($response->getRequestMethod())->toBe('GET')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid/addresses/address-uuid');
});

it('creates a domain address', function () {
    $response = jsonResponse([
        'data' => domainAddressData(),
    ]);
    [$client] = createClient([$response]);

    $address = $client->domains()->addresses(domainData()['uuid'])->create(createDomainAddressData());

    expect($address)->toBeInstanceOf(DomainAddressModel::class)
        ->and($address->uuid())->toBe(domainAddressData()['uuid'])
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid/addresses')
        ->and($response->getRequestOptions()['body'])->toBe(json_encode(createDomainAddressData(), JSON_THROW_ON_ERROR))
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines([
            'Content-Type' => 'application/json',
        ]));
});

it('deletes a domain address by uuid', function () {
    $response = emptyResponse();
    [$client] = createClient([$response]);

    $result = $client->domains()->addresses(domainData()['uuid'])->delete(domainAddressData()['uuid']);

    expect($result)->toBeNull()
        ->and($response->getRequestMethod())->toBe('DELETE')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid/addresses/address-uuid');
});

it('deletes an attached domain address model', function () {
    $getResponse = jsonResponse([
        'data' => domainAddressData(),
    ]);
    $deleteResponse = emptyResponse();
    [$client] = createClient([$getResponse, $deleteResponse]);

    $address = $client->domains()->addresses(domainData()['uuid'])->get(domainAddressData()['uuid']);
    $result = $address->delete();

    expect($result)->toBeNull()
        ->and($deleteResponse->getRequestMethod())->toBe('DELETE')
        ->and($deleteResponse->getRequestUrl())->toBe(testBaseUrl() . '/domains/domain-uuid/addresses/address-uuid');
});
