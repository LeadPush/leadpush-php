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
     * Access event API operations for a contact.
     */
    public function events(string $contactId): ContactEvents
    {
        return new ContactEvents($this->client, $contactId);
    }
}
