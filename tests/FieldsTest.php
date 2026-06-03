<?php

declare(strict_types=1);

use Leadpush\SDK\Models\FieldModel;

use function Leadpush\SDK\Test\Support\createClient;
use function Leadpush\SDK\Test\Support\createFieldData;
use function Leadpush\SDK\Test\Support\fieldData;
use function Leadpush\SDK\Test\Support\fieldFilters;
use function Leadpush\SDK\Test\Support\jsonResponse;
use function Leadpush\SDK\Test\Support\testBaseUrl;

it('gets a field by uuid', function () {
    $response = jsonResponse([
        'data' => fieldData(),
    ]);
    [$client] = createClient([$response]);

    $field = $client->fields()->get(fieldData()['uuid']);

    expect($field)->toBeInstanceOf(FieldModel::class)
        ->and($field->uuid())->toBe(fieldData()['uuid'])
        ->and($field->name())->toBe(fieldData()['name'])
        ->and($field->type())->toBe('text')
        ->and($field->format()['text'])->toBe('url')
        ->and($field->createdAt())->toEqual(new DateTimeImmutable(fieldData()['created_at']))
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/fields/field-uuid');
});

it('creates a field', function () {
    $response = jsonResponse([
        'data' => fieldData(),
    ]);
    [$client] = createClient([$response]);

    $field = $client->fields()->create(createFieldData());

    expect($field)->toBeInstanceOf(FieldModel::class)
        ->and($field->uuid())->toBe(fieldData()['uuid'])
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/fields')
        ->and($response->getRequestOptions()['body'])->toBe(json_encode(createFieldData(), JSON_THROW_ON_ERROR));
});

it('lists fields with search and filters', function () {
    $response = jsonResponse([
        'data' => [fieldData()],
        'meta' => [
            'current_page' => 1,
            'per_page' => 10,
            'total' => 1,
            'last_page' => 1,
            'has_next' => false,
        ],
    ]);
    [$client] = createClient([$response]);

    $fields = $client->fields()->list([
        'search' => 'company',
        'filters' => fieldFilters(),
        'page' => 1,
        'per_page' => 10,
    ]);

    expect($fields->data()[0])->toBeInstanceOf(FieldModel::class)
        ->and($fields->data()[0]->name())->toBe(fieldData()['name'])
        ->and($fields->meta()->total())->toBe(1)
        ->and($response->getRequestUrl())->toBe(
            testBaseUrl() . '/fields?search=company&filters=' . rawurlencode(json_encode(fieldFilters(), JSON_THROW_ON_ERROR)) . '&page=1&per_page=10',
        );
});
