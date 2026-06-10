<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Entity;
use Leadpush\SDK\Models\ContactModel;

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
