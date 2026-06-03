<?php

declare(strict_types=1);

namespace Leadpush\SDK;

/**
 * Base class for Leadpush API response models.
 */
abstract class Model
{
    /**
     * @var array<string, mixed>
     */
    private array $dirty = [];

    /**
     * @param array<string, mixed> $data Raw API data backing the model.
     */
    public function __construct(
        protected array $data,
        protected readonly ?ModelContext $context = null,
    ) {
    }

    /**
     * Return the raw API data backing this model.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Return the raw API data backing this model.
     *
     * @return array<string, mixed>
     */
    public function toJSON(): array
    {
        return $this->toArray();
    }

    /**
     * Clear all locally tracked changes.
     */
    protected function clearDirty(): void
    {
        $this->dirty = [];
    }

    /**
     * Return the locally tracked changes for update requests.
     *
     * @return array<string, mixed>
     */
    protected function getDirty(): array
    {
        return $this->dirty;
    }

    /**
     * Determine whether the model has local changes.
     */
    protected function isDirty(): bool
    {
        return $this->dirty !== [];
    }

    /**
     * Replace the raw API data backing this model.
     *
     * @param array<string, mixed> $data
     */
    protected function replaceData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Return the attached API context or throw when the model is detached.
     */
    protected function requireContext(): ModelContext
    {
        if ($this->context === null) {
            throw new \RuntimeException('This model is not attached to an API client.');
        }

        return $this->context;
    }

    /**
     * Mark one update payload key as dirty.
     */
    protected function setDirty(string $key, mixed $value): void
    {
        $this->dirty[$key] = $value;
    }

    /**
     * Make a GET request relative to the model's parent resource.
     *
     * @param string|array<int, string> $path
     * @param array<string, mixed> $params
     */
    protected function get(string|array $path, array $params = []): mixed
    {
        return $this->requireContext()->get($path, $params);
    }

    /**
     * Make a POST request relative to the model's parent resource.
     *
     * @param string|array<int, string> $path
     * @param array<string, mixed>|null $data
     * @param array<string, mixed> $params
     */
    protected function post(string|array $path, ?array $data = null, array $params = []): mixed
    {
        return $this->requireContext()->post($path, $data, $params);
    }

    /**
     * Make a DELETE request relative to the model's parent resource.
     *
     * @param string|array<int, string> $path
     * @param array<string, mixed> $params
     */
    protected function delete(string|array $path, array $params = []): mixed
    {
        return $this->requireContext()->delete($path, $params);
    }
}
