<?php

declare(strict_types=1);

use Leadpush\SDK\Exceptions\TimeoutError;
use Leadpush\SDK\Leadpush;
use Symfony\Component\HttpClient\Exception\TimeoutException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

use function Leadpush\SDK\Test\Support\createClient;
use function Leadpush\SDK\Test\Support\emptyResponse;
use function Leadpush\SDK\Test\Support\expectedHeaderLines;
use function Leadpush\SDK\Test\Support\jsonResponse;
use function Leadpush\SDK\Test\Support\requestHeaders;
use function Leadpush\SDK\Test\Support\testBaseUrl;

it('makes a DELETE request', function () {
    $response = jsonResponse([
        'deleted' => true,
    ]);
    [$client] = createClient([$response]);

    $result = $client->delete(['contacts', 'contact-id'], [
        'force' => true,
    ]);

    expect($result)->toBe(['deleted' => true])
        ->and($response->getRequestMethod())->toBe('DELETE')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts/contact-id?force=true')
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines());
});

it('passes POST bodies and additional headers', function () {
    $response = emptyResponse();
    [$client] = createClient([$response], options: [
        'headers' => [
            'X-Test-Header' => 'test-value',
        ],
        'userAgent' => 'test-agent',
    ]);

    $client->post('contacts', [
        'subscribed' => true,
    ]);

    expect($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/contacts')
        ->and($response->getRequestOptions()['body'])->toBe('{"subscribed":true}')
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines([
            'X-Test-Header' => 'test-value',
            'Content-Type' => 'application/json',
        ], userAgent: 'test-agent'));
});

it('throws a timeout error when Symfony reports a timeout', function () {
    $http = new class implements HttpClientInterface {
        public array $requestOptions = [];

        public function request(string $method, string $url, array $options = []): ResponseInterface
        {
            $this->requestOptions = $options;

            throw new TimeoutException('timed out');
        }

        public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
        {
            throw new LogicException('Not used.');
        }

        public function withOptions(array $options): static
        {
            return $this;
        }
    };
    $client = new Leadpush('test-key', [
        'baseUrl' => testBaseUrl(),
        'timeout' => 10,
    ], $http);

    expect(fn () => $client->get('contacts'))->toThrow(TimeoutError::class, 'Leadpush API request timed out after 10ms.')
        ->and($http->requestOptions['max_duration'])->toBe(0.01);
});
