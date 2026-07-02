<?php

declare(strict_types=1);

namespace Leadpush\SDK;

use Composer\InstalledVersions;
use Leadpush\SDK\Resources\Contacts;
use Leadpush\SDK\Resources\Domains;
use Leadpush\SDK\Resources\Emails;
use Leadpush\SDK\Resources\Fields;
use Leadpush\SDK\Resources\Suppressions;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Leadpush API client.
 */
class Leadpush
{
    /**
     * Name of this SDK package.
     */
    public const SDK_NAME = 'leadpush/sdk-php';

    /**
     * Leadpush API version used by this SDK.
     */
    public const API_VERSION = 'v1';

    /**
     * Default Leadpush API base URL.
     */
    public const DEFAULT_BASE_URL = 'https://api.leadpush.io/v1';

    /**
     * Default request timeout in milliseconds.
     */
    public const DEFAULT_TIMEOUT = 30000;

    private readonly string $version;

    /**
     * @var array{baseUrl: string, timeout: int, headers: array<string, string>, userAgent: string}
     */
    private readonly array $options;

    private readonly Http $http;

    /**
     * Create a Leadpush API client.
     *
     * @param string $key Leadpush API key.
     * @param array{baseUrl?: string, timeout?: int, headers?: array<string, string>, userAgent?: string} $options Optional client configuration.
     * @param HttpClientInterface|null $httpClient Optional Symfony HTTP client for custom transports or tests.
     */
    public function __construct(
        private readonly string $key,
        array $options = [],
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->version = self::resolveVersion();

        $this->options = array_replace([
            'baseUrl' => self::DEFAULT_BASE_URL,
            'timeout' => self::DEFAULT_TIMEOUT,
            'headers' => [],
            'userAgent' => self::defaultUserAgent($this->version),
        ], $options);

        $this->http = new Http($httpClient ?? SymfonyHttpClient::create(), [
            'apiKey' => $this->key,
            'apiVersion' => self::API_VERSION,
            'baseUrl' => $this->options['baseUrl'],
            'headers' => $this->options['headers'],
            'sdkName' => self::SDK_NAME,
            'sdkVersion' => $this->version,
            'timeout' => $this->options['timeout'],
            'userAgent' => $this->options['userAgent'],
        ]);
    }

    /**
     * Build the default user agent for this SDK.
     */
    public static function defaultUserAgent(?string $version = null): string
    {
        return self::SDK_NAME . '/' . ($version ?? self::resolveVersion()) . ' (api=' . self::API_VERSION . ')';
    }

    /**
     * Return the API key configured for this client.
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Return the installed SDK version.
     */
    public function version(): string
    {
        return $this->version;
    }

    /**
     * Return the runtime options used by this client.
     *
     * @return array{baseUrl: string, timeout: int, headers: array<string, string>, userAgent: string}
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * Make a GET request to the Leadpush API.
     *
     * @param string|array<int, string> $path API path relative to the configured base URL.
     * @param array<string, mixed> $params Optional query parameters.
     */
    public function get(string|array $path, array $params = []): mixed
    {
        return $this->http->get($path, $params);
    }

    /**
     * Make a POST request to the Leadpush API.
     *
     * @param string|array<int, string> $path API path relative to the configured base URL.
     * @param array<string, mixed>|null $data Optional JSON request body.
     * @param array<string, mixed> $params Optional query parameters.
     */
    public function post(string|array $path, ?array $data = null, array $params = []): mixed
    {
        return $this->http->post($path, $data, $params);
    }

    /**
     * Make a DELETE request to the Leadpush API.
     *
     * @param string|array<int, string> $path API path relative to the configured base URL.
     * @param array<string, mixed> $params Optional query parameters.
     */
    public function delete(string|array $path, array $params = []): mixed
    {
        return $this->http->delete($path, $params);
    }

    /**
     * Access contact API operations.
     */
    public function contacts(): Contacts
    {
        return new Contacts($this);
    }

    /**
     * Access domain API operations.
     */
    public function domains(): Domains
    {
        return new Domains($this);
    }

    /**
     * Access email sending API operations.
     */
    public function emails(): Emails
    {
        return new Emails($this);
    }

    /**
     * Access custom field API operations.
     */
    public function fields(): Fields
    {
        return new Fields($this);
    }

    /**
     * Access suppression API operations.
     */
    public function suppressions(): Suppressions
    {
        return new Suppressions($this);
    }

    /**
     * Resolve the installed SDK version from Composer metadata.
     */
    private static function resolveVersion(): string
    {
        try {
            return InstalledVersions::getPrettyVersion(self::SDK_NAME) ?: 'dev-main';
        } catch (\Throwable) {
            return 'dev-main';
        }
    }
}
