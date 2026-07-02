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

function domainData(): array
{
    return [
        'uuid' => 'domain-uuid',
        'name' => 'example.test',
        'domain' => 'example.test',
        'verified' => false,
        'provider' => 'leadpush',
        'status' => 'pending',
        'verification' => 'pending',
        'mail_from_domain' => 'bounces.example.test',
        'mail_from_verified' => false,
        'dns' => [
            [
                'type' => 'CNAME',
                'name' => 'default._domainkey.example.test',
                'value' => 'default._domainkey.smtp-domain-1.leadpush.net.',
                'is_valid' => false,
            ],
            [
                'type' => 'MX',
                'name' => 'bounces.example.test',
                'value' => '10 bounces.leadpush.net',
                'is_valid' => false,
            ],
        ],
        'updated_at' => '2026-06-20T12:00:00.000Z',
        'created_at' => '2026-06-20T12:00:00.000Z',
    ];
}

function verifiedDomainData(): array
{
    $data = domainData();
    $data['verified'] = true;
    $data['verification'] = 'completed';
    $data['mail_from_verified'] = true;
    $data['dns'] = array_map(function (array $record): array {
        $record['is_valid'] = true;

        return $record;
    }, $data['dns']);
    $data['updated_at'] = '2026-06-20T12:05:00.000Z';

    return $data;
}

function createDomainData(): array
{
    return [
        'name' => 'example.test',
        'dkim_selectors' => ['default'],
        'tracking_subdomain' => 'click',
        'tracking_mode' => 'cloudflare',
    ];
}

function domainAddressData(): array
{
    return [
        'uuid' => 'address-uuid',
        'domain_uuid' => domainData()['uuid'],
        'address' => 'sender',
        'full_address' => 'sender@example.test',
        'provider' => 'leadpush',
        'display_name' => 'Sender Name',
        'verification' => 'completed',
        'updated_at' => '2026-06-20T12:10:00.000Z',
        'created_at' => '2026-06-20T12:10:00.000Z',
    ];
}

function createDomainAddressData(): array
{
    return [
        'address' => 'sender',
        'display_name' => 'Sender Name',
        'reply_to' => 'reply@example.test',
        'company_address' => '123 Main St',
        'company_address_2' => null,
        'company_city' => 'New York',
        'company_state' => 'NY',
        'company_zip' => '10001',
        'company_country' => 'US',
    ];
}

function createEmailData(): array
{
    return [
        'from' => 'sender@developer.test',
        'subject' => 'Developer API email',
        'html' => '<p>Hello world</p>',
        'text' => 'Hello world',
        'to' => [
            'known@example.test',
            'other@example.test',
            'third@example.test',
        ],
        'bcc' => [
            'audit@example.test',
        ],
        'reply_to' => 'reply@example.test',
        'headers' => [
            'X-Correlation-ID' => 'abc-123',
            'Auto-Submitted' => 'auto-generated',
        ],
    ];
}

function emailSendData(): array
{
    return [
        'accepted' => true,
        'message_count' => 4,
        'messages' => [
            [
                'uuid' => 'message-known-uuid',
                'recipient' => 'known@example.test',
                'type' => 'to',
                'from' => 'sender@developer.test',
                'status' => 'pending',
            ],
            [
                'uuid' => 'message-other-uuid',
                'recipient' => 'other@example.test',
                'type' => 'to',
                'from' => 'sender@developer.test',
                'status' => 'pending',
            ],
            [
                'uuid' => 'message-third-uuid',
                'recipient' => 'third@example.test',
                'type' => 'to',
                'from' => 'sender@developer.test',
                'status' => 'pending',
            ],
            [
                'uuid' => 'message-audit-uuid',
                'recipient' => 'audit@example.test',
                'type' => 'bcc',
                'from' => 'sender@developer.test',
                'status' => 'pending',
            ],
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
