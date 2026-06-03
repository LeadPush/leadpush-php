<?php

declare(strict_types=1);

namespace Leadpush\SDK;

/**
 * Base class for Leadpush API entities that support CRUD-style operations.
 *
 * @internal
 */
abstract class Entity extends ListableResource
{
    /**
     * Create a resource.
     *
     * @param array<string, mixed> $data Resource creation payload.
     */
    public function create(array $data): Model
    {
        $payload = $this->postResource(null, $data);

        return $this->makeModel($payload['data']);
    }

    /**
     * Update a resource by id.
     *
     * @param array<string, mixed> $data Resource update payload.
     */
    public function update(string $id, array $data): Model
    {
        $payload = $this->postResource([$id], $data);

        return $this->makeModel($payload['data']);
    }

    /**
     * Get a resource by id.
     */
    public function get(string $id): Model
    {
        $payload = $this->getResource([$id]);

        return $this->makeModel($payload['data']);
    }

    /**
     * Update a model's backing resource.
     *
     * @param array<string, mixed> $data
     */
    protected function updateModel(string $id, array $data): Model
    {
        return $this->update($id, $data);
    }
}
