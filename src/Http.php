<?php

declare(strict_types=1);

namespace Leadpush\SDK;

use Leadpush\SDK\Exceptions\ApiError;
use Leadpush\SDK\Exceptions\ForbiddenError;
use Leadpush\SDK\Exceptions\NotFoundError;
use Leadpush\SDK\Exceptions\TimeoutError;
use Leadpush\SDK\Exceptions\UnauthorizedError;
use Leadpush\SDK\Exceptions\ValidationError;
use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Internal HTTP client used by Leadpush resources.
 *
 * @internal
 */
class Http
{
    /**
     * @param array{
     *     apiKey: string,
     *     apiVersion: string,
     *     baseUrl: string,
     *     headers: array<string, string>,
     *     sdkName: string,
     *     sdkVersion: string,
     *     timeout: int,
     *     userAgent: string
     * } $options
     */
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly array $options,
    ) {
    }

    /**
     * Make a GET request.
     *
     * @param string|array<int, string> $path API path relative to the configured base URL.
     * @param array<string, mixed> $params Optional query parameters.
     */
    public function get(string|array $path, array $params = []): mixed
    {
        return $this->request('GET', $path, null, $params);
    }

    /**
     * Make a POST request.
     *
     * @param string|array<int, string> $path API path relative to the configured base URL.
     * @param array<string, mixed>|null $data Optional JSON request body.
     * @param array<string, mixed> $params Optional query parameters.
     */
    public function post(string|array $path, ?array $data = null, array $params = []): mixed
    {
        return $this->request('POST', $path, $data, $params);
    }

    /**
     * Make a DELETE request.
     *
     * @param string|array<int, string> $path API path relative to the configured base URL.
     * @param array<string, mixed> $params Optional query parameters.
     */
    public function delete(string|array $path, array $params = []): mixed
    {
        return $this->request('DELETE', $path, null, $params);
    }

    /**
     * @param string|array<int, string> $path
     * @param array<string, mixed>|null $data
     * @param array<string, mixed> $params
     */
    private function request(string $method, string|array $path, ?array $data, array $params): mixed
    {
        $requestOptions = [
            'headers' => $this->headers($data),
        ];

        if ($data !== null) {
            $requestOptions['body'] = json_encode($data, JSON_THROW_ON_ERROR);
        }

        $timeout = (int) $this->options['timeout'];

        if ($timeout > 0) {
            $requestOptions['max_duration'] = $timeout / 1000;
        }

        try {
            $response = $this->client->request($method, $this->url($path, $params), $requestOptions);

            return $this->parseResponse($response);
        } catch (TimeoutExceptionInterface $exception) {
            throw new TimeoutError($timeout, $exception);
        }
    }

    /**
     * @param string|array<int, string> $path
     * @param array<string, mixed> $params
     */
    private function url(string|array $path, array $params): string
    {
        $baseUrl = rtrim((string) $this->options['baseUrl'], '/');
        $segments = array_map(rawurlencode(...), $this->pathSegments($path));
        $url = $baseUrl . ($segments === [] ? '' : '/' . implode('/', $segments));
        $query = $this->query($params);

        return $query === '' ? $url : $url . '?' . $query;
    }

    /**
     * @param string|array<int, string> $path
     * @return array<int, string>
     */
    private function pathSegments(string|array $path): array
    {
        $segments = is_string($path) ? [$path] : $path;
        $parts = [];

        foreach ($segments as $segment) {
            foreach (explode('/', (string) $segment) as $part) {
                if ($part !== '') {
                    $parts[] = $part;
                }
            }
        }

        return $parts;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function query(array $params): string
    {
        $pairs = [];

        foreach ($params as $key => $value) {
            if ($value === null) {
                continue;
            }

            $pairs[] = rawurlencode((string) $key) . '=' . rawurlencode($this->queryValue($value));
        }

        return implode('&', $pairs);
    }

    /**
     * Serialize a query parameter using the Node SDK-compatible rules.
     */
    private function queryValue(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    /**
     * Build request headers for an API request.
     *
     * @param array<string, mixed>|null $data
     * @return array<string, string>
     */
    private function headers(?array $data): array
    {
        $headers = array_replace([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->options['apiKey'],
            'X-Leadpush-API-Version' => $this->options['apiVersion'],
            'X-Leadpush-SDK' => $this->options['sdkName'],
            'X-Leadpush-SDK-Version' => $this->options['sdkVersion'],
        ], $this->options['headers']);

        $headers['User-Agent'] = $this->options['userAgent'];

        if ($data !== null) {
            $headers['Content-Type'] = 'application/json';
        }

        return $headers;
    }

    /**
     * Parse a Symfony response and map API failures to SDK exceptions.
     */
    private function parseResponse(ResponseInterface $response): mixed
    {
        $status = $response->getStatusCode();
        $payload = $this->parseResponseBody($response);

        if ($status >= 200 && $status < 300) {
            return $payload;
        }

        throw match ($status) {
            401 => new UnauthorizedError($payload),
            403 => new ForbiddenError($payload),
            404 => new NotFoundError($payload),
            422 => new ValidationError($payload),
            default => new ApiError($status, $payload),
        };
    }

    /**
     * Parse empty, JSON, and plain-text response bodies.
     */
    private function parseResponseBody(ResponseInterface $response): mixed
    {
        $text = $response->getContent(false);

        if ($text === '') {
            return null;
        }

        try {
            return json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $text;
        }
    }
}
