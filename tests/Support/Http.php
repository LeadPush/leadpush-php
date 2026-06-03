<?php

declare(strict_types=1);

namespace Leadpush\SDK\Test\Support;

use Leadpush\SDK\Leadpush;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

function testBaseUrl(): string
{
    return 'https://api.leadpush.test/v1';
}

function jsonResponse(array $payload, int $status = 200): MockResponse
{
    return new MockResponse(json_encode($payload, JSON_THROW_ON_ERROR), [
        'http_code' => $status,
        'response_headers' => [
            'content-type' => 'application/json',
        ],
    ]);
}

function emptyResponse(int $status = 204): MockResponse
{
    return new MockResponse('', [
        'http_code' => $status,
    ]);
}

function createClient(array $responses = [], string $key = 'test-key', array $options = []): array
{
    $http = new MockHttpClient($responses);
    $client = new Leadpush($key, array_replace([
        'baseUrl' => testBaseUrl(),
        'timeout' => 0,
    ], $options), $http);

    return [$client, $http];
}

function expectedHeaderLines(array $headers = [], string $key = 'test-key', string $userAgent = ''): array
{
    $userAgent = $userAgent === '' ? Leadpush::defaultUserAgent('dev-main') : $userAgent;

    return array_merge([
        'Accept: application/json',
        "Authorization: Bearer {$key}",
        'X-Leadpush-API-Version: ' . Leadpush::API_VERSION,
        'X-Leadpush-SDK: ' . Leadpush::SDK_NAME,
        'X-Leadpush-SDK-Version: dev-main',
        'User-Agent: ' . $userAgent,
    ], array_map(
        fn (string $name, string $value): string => "{$name}: {$value}",
        array_keys($headers),
        $headers,
    ));
}

function requestHeaders(MockResponse $response): array
{
    return $response->getRequestOptions()['headers'] ?? [];
}
