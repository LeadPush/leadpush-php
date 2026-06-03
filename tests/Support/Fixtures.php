<?php

declare(strict_types=1);

namespace Leadpush\SDK\Test\Support;

function contactData(): array
{
    return [
        'uuid' => '69474ed13511f060ca09781a',
        'subscribed' => true,
        'attributes' => [
            'email' => 'contact@example.com',
            'phone' => '5551234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'mailchimp' => null,
            'test_date' => null,
            'test_number' => 2342,
        ],
        'provider' => 'gmail',
        'updated_at' => '2026-03-27T16:50:43.106000Z',
        'created_at' => '2025-12-21T01:35:13.531000Z',
    ];
}

function updatedContactData(): array
{
    $data = contactData();
    $data['attributes']['first_name'] = 'Updated';

    return $data;
}

function unsubscribedContactData(): array
{
    $data = contactData();
    $data['subscribed'] = false;

    return $data;
}

function createContactData(): array
{
    return [
        'subscribed' => true,
        'attributes' => [
            'email' => 'created@example.com',
            'phone' => null,
            'first_name' => 'Created',
            'last_name' => 'Contact',
        ],
    ];
}

function updateContactData(): array
{
    return [
        'attributes' => [
            'first_name' => 'Updated',
        ],
    ];
}

function contactEventData(): array
{
    return [
        'uuid' => '6a19c3c2673f43e71a0f3882',
        'event_name' => 'purchase',
        'attributes' => [
            'plan' => 'enterprise',
        ],
        'created_at' => '2026-05-29T16:50:10.000000Z',
    ];
}

function createContactEventData(): array
{
    return [
        'event_name' => 'purchase',
        'attributes' => [
            'plan' => 'enterprise',
        ],
    ];
}

function fieldData(): array
{
    return [
        'uuid' => 'field-uuid',
        'name' => 'company_name',
        'type' => 'text',
        'format' => [
            'text' => 'url',
            'pattern' => null,
            'iso_format' => null,
        ],
        'created_at' => '2021-01-01T00:00:00.000Z',
    ];
}

function createFieldData(): array
{
    return [
        'name' => 'company_name',
        'type' => 'text',
        'format' => [
            'text' => 'url',
        ],
    ];
}

function fieldFilters(): array
{
    return [
        [
            'id' => 'type',
            'value' => ['text'],
        ],
    ];
}

function suppressionData(): array
{
    return [
        'uuid' => 'suppression-id',
        'email' => 'blocked@example.test',
        'type' => 'manual',
        'created_at' => '2021-01-01T00:00:00.000Z',
    ];
}

function suppressionFilters(): array
{
    return [
        [
            'id' => 'type',
            'value' => ['bounce'],
        ],
    ];
}
