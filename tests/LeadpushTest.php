<?php

declare(strict_types=1);

use Leadpush\SDK\Leadpush;
use Symfony\Component\HttpClient\MockHttpClient;

use function Leadpush\SDK\Test\Support\installedSdkVersion;

it('exposes the SDK version and key', function () {
    $client = new Leadpush('test-key', httpClient: new MockHttpClient());

    expect($client->version())->toBe(installedSdkVersion())
        ->and($client->key())->toBe('test-key');
});

it('sets production request defaults', function () {
    $client = new Leadpush('test-key', httpClient: new MockHttpClient());

    expect($client->options())->toBe([
        'baseUrl' => Leadpush::DEFAULT_BASE_URL,
        'timeout' => Leadpush::DEFAULT_TIMEOUT,
        'headers' => [],
        'userAgent' => Leadpush::defaultUserAgent(installedSdkVersion()),
    ]);
});

it('allows request defaults to be overridden', function () {
    $client = new Leadpush('test-key', [
        'baseUrl' => 'https://api.example.test/v2',
        'headers' => [
            'X-Custom-Header' => 'custom-value',
        ],
        'timeout' => 1000,
        'userAgent' => 'custom-agent',
    ], new MockHttpClient());

    expect($client->options())->toBe([
        'baseUrl' => 'https://api.example.test/v2',
        'timeout' => 1000,
        'headers' => [
            'X-Custom-Header' => 'custom-value',
        ],
        'userAgent' => 'custom-agent',
    ]);
});
