<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Entity;
use Leadpush\SDK\Models\ContactModel;
use Leadpush\SDK\Responses\PaginatedResponse;

/**
 * Contact API resource.
 */
class Contacts extends Entity
{
    /**
     * Return the API path segment for contacts.
     */
    protected function endpoint(): string|array
    {
        return 'contacts';
    }

    /**
     * Return the model class used to wrap contact data.
     *
     * @return class-string<ContactModel>
     */
    protected function modelClass(): string
    {
        return ContactModel::class;
    }

    /**
     * @param array{
     *     search?: string,
     *     filters?: array<int, array{id: 'provider'|'subscribed', value: array<int, string|bool>}>,
     *     page?: int,
     *     perPage?: int,
     *     per_page?: int
     * } $params
     */
    public function list(array $params = []): PaginatedResponse
    {
        return parent::list(array_filter([
            'search' => $params['search'] ?? null,
            'filters' => isset($params['filters']) ? json_encode($params['filters'], JSON_THROW_ON_ERROR) : null,
            'page' => $params['page'] ?? null,
            'per_page' => $params['perPage'] ?? $params['per_page'] ?? null,
        ], static fn ($value) => $value !== null));
    }

    /**
     * Get a contact by uuid or workspace identity value.
     */
    public function get(string $identifier): ContactModel
    {
        return parent::get($identifier);
    }

    /**
     * Update a contact by uuid or workspace identity value.
     *
     * @param array<string, mixed> $data Contact update payload.
     */
    public function update(string $identifier, array $data): ContactModel
    {
        return parent::update($identifier, $data);
    }

    /**
     * Subscribe a contact by uuid or workspace identity value.
     */
    public function subscribe(string $identifier): ContactModel
    {
        $payload = $this->postResource([$identifier, 'subscribe']);

        return $this->makeModel($payload['data']);
    }

    /**
     * Unsubscribe a contact by uuid or workspace identity value.
     */
    public function unsubscribe(string $identifier): ContactModel
    {
        $payload = $this->postResource([$identifier, 'unsubscribe']);

        return $this->makeModel($payload['data']);
    }

    /**
     * Access event API operations for a contact by uuid or workspace identity value.
     */
    public function events(string $identifier): ContactEvents
    {
        return new ContactEvents($this->client, $identifier);
    }
}
