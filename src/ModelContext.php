<?php

declare(strict_types=1);

namespace Leadpush\SDK;

/**
 * Narrow API context attached to models returned by the SDK.
 *
 * @internal
 */
class ModelContext
{
    private readonly \Closure $get;
    private readonly \Closure $post;
    private readonly \Closure $delete;
    private readonly \Closure $update;

    /**
     * @param callable(string|array<int, string>, array<string, mixed>): mixed $get
     * @param callable(string|array<int, string>, array<string, mixed>|null, array<string, mixed>): mixed $post
     * @param callable(string|array<int, string>, array<string, mixed>): mixed $delete
     * @param callable(string, array<string, mixed>): Model $update
     */
    public function __construct(
        private readonly Leadpush $client,
        callable $get,
        callable $post,
        callable $delete,
        callable $update,
    ) {
        $this->get = \Closure::fromCallable($get);
        $this->post = \Closure::fromCallable($post);
        $this->delete = \Closure::fromCallable($delete);
        $this->update = \Closure::fromCallable($update);
    }

    /**
     * Return the client that created the model.
     */
    public function client(): Leadpush
    {
        return $this->client;
    }

    /**
     * Make a GET request relative to the model's parent resource.
     *
     * @param string|array<int, string> $path
     * @param array<string, mixed> $params
     */
    public function get(string|array $path, array $params = []): mixed
    {
        return ($this->get)($path, $params);
    }

    /**
     * Make a POST request relative to the model's parent resource.
     *
     * @param string|array<int, string> $path
     * @param array<string, mixed>|null $data
     * @param array<string, mixed> $params
     */
    public function post(string|array $path, ?array $data = null, array $params = []): mixed
    {
        return ($this->post)($path, $data, $params);
    }

    /**
     * Make a DELETE request relative to the model's parent resource.
     *
     * @param string|array<int, string> $path
     * @param array<string, mixed> $params
     */
    public function delete(string|array $path, array $params = []): mixed
    {
        return ($this->delete)($path, $params);
    }

    /**
     * Update the model's backing resource.
     *
     * @param array<string, mixed> $data
     */
    public function update(string $id, array $data): Model
    {
        return ($this->update)($id, $data);
    }
}
