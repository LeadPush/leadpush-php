<?php

declare(strict_types=1);

namespace Leadpush\SDK;

use Leadpush\SDK\Exceptions\UnsupportedEndpointError;

/**
 * Base class for Leadpush API resources.
 *
 * @internal
 */
abstract class Resource
{
    /**
     * Create a resource handler.
     */
    public function __construct(
        protected readonly Leadpush $client,
    ) {
    }

    /**
     * Return the API path for this resource.
     *
     * @return string|array<int, string>
     */
    abstract protected function endpoint(): string|array;

    /**
     * Return the model class used to wrap resource response data.
     *
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * Wrap raw response data in an attached model.
     *
     * @param array<string, mixed> $data
     */
    protected function makeModel(array $data): Model
    {
        $modelClass = $this->modelClass();

        return new $modelClass($data, new ModelContext(
            $this->client,
            fn (string|array $path, array $params = []) => $this->getResource($path, $params),
            fn (string|array $path, ?array $data = null, array $params = []) => $this->postResource($path, $data, $params),
            fn (string|array $path, array $params = []) => $this->deleteResource($path, $params),
            fn (string $id, array $data) => $this->updateModel($id, $data),
        ));
    }

    /**
     * Make a GET request relative to the resource endpoint.
     *
     * @param string|array<int, string>|null $path
     * @param array<string, mixed> $params
     */
    protected function getResource(string|array|null $path = null, array $params = []): mixed
    {
        return $this->client->get($this->resourcePath($path), $params);
    }

    /**
     * Make a POST request relative to the resource endpoint.
     *
     * @param string|array<int, string>|null $path
     * @param array<string, mixed>|null $data
     * @param array<string, mixed> $params
     */
    protected function postResource(string|array|null $path = null, ?array $data = null, array $params = []): mixed
    {
        return $this->client->post($this->resourcePath($path), $data, $params);
    }

    /**
     * Make a DELETE request relative to the resource endpoint.
     *
     * @param string|array<int, string>|null $path
     * @param array<string, mixed> $params
     */
    protected function deleteResource(string|array|null $path = null, array $params = []): mixed
    {
        return $this->client->delete($this->resourcePath($path), $params);
    }

    /**
     * Update a model's backing resource.
     *
     * @param array<string, mixed> $data
     */
    protected function updateModel(string $id, array $data): Model
    {
        throw new UnsupportedEndpointError('This resource does not support model updates.');
    }

    /**
     * @param string|array<int, string>|null $path
     * @return array<int, string>
     */
    private function resourcePath(string|array|null $path): array
    {
        $segments = $this->pathSegments($this->endpoint());

        if ($path === null) {
            return $segments;
        }

        return array_merge($segments, $this->pathSegments($path));
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
}
