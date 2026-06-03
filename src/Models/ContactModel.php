<?php

declare(strict_types=1);

namespace Leadpush\SDK\Models;

use Leadpush\SDK\Model;
use Leadpush\SDK\Resources\ContactEvents;

/**
 * Contact returned by the Leadpush API.
 */
class ContactModel extends Model
{
    /**
     * Contact id.
     */
    public function uuid(): string
    {
        return (string) $this->data['uuid'];
    }

    /**
     * Whether the contact is currently subscribed.
     */
    public function isSubscribed(): bool
    {
        return (bool) $this->data['subscribed'];
    }

    /**
     * Update the local subscribed value and mark it dirty for update().
     */
    public function setSubscribed(bool $value): void
    {
        $this->data['subscribed'] = $value;
        $this->setDirty('subscribed', $value);
    }

    /**
     * Contact attributes returned by the API.
     *
     * @return array<string, string|int|float|bool|null>
     */
    public function attributes(): array
    {
        return $this->data['attributes'] ?? [];
    }

    /**
     * Update one contact attribute and mark it dirty for update().
     */
    public function setAttribute(string $key, string|int|float|bool|null $value): void
    {
        $attributes = $this->attributes();
        $attributes[$key] = $value;
        $this->data['attributes'] = $attributes;

        $dirty = $this->getDirty();
        $dirtyAttributes = is_array($dirty['attributes'] ?? null) ? $dirty['attributes'] : [];
        $dirtyAttributes[$key] = $value;

        $this->setDirty('attributes', $dirtyAttributes);
    }

    /**
     * Contact provider detected by the API, when available.
     */
    public function provider(): ?string
    {
        return $this->data['provider'] ?? null;
    }

    /**
     * Contact creation date.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this->data['created_at']);
    }

    /**
     * Contact last update date.
     */
    public function updatedAt(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this->data['updated_at']);
    }

    /**
     * Persist dirty local changes to the API.
     */
    public function update(): self
    {
        if (! $this->isDirty()) {
            return $this;
        }

        $updated = $this->requireContext()->update($this->uuid(), $this->getDirty());

        if (! $updated instanceof self) {
            throw new \RuntimeException('Contact update did not return a contact model.');
        }

        $this->replaceData($updated->toArray());
        $this->clearDirty();

        return $this;
    }

    /**
     * Subscribe the contact.
     */
    public function subscribe(): self
    {
        $payload = $this->post([$this->uuid(), 'subscribe']);
        $this->replaceData($payload['data']);
        $this->clearDirty();

        return $this;
    }

    /**
     * Unsubscribe the contact.
     */
    public function unsubscribe(): self
    {
        $payload = $this->post([$this->uuid(), 'unsubscribe']);
        $this->replaceData($payload['data']);
        $this->clearDirty();

        return $this;
    }

    /**
     * Access event API operations for this contact.
     */
    public function events(): ContactEvents
    {
        return new ContactEvents($this->requireContext()->client(), $this->uuid());
    }
}
