<?php

declare(strict_types=1);

namespace Leadpush\SDK\Models;

use Leadpush\SDK\Model;

/**
 * Contact event returned by the Leadpush API.
 */
class ContactEventModel extends Model
{
    /**
     * Event id.
     */
    public function uuid(): string
    {
        return (string) $this->data['uuid'];
    }

    /**
     * Event name.
     */
    public function eventName(): string
    {
        return (string) $this->data['event_name'];
    }

    /**
     * Event name.
     *
     * @deprecated Use eventName().
     */
    public function type(): string
    {
        return $this->eventName();
    }

    /**
     * Event attributes.
     *
     * @return array<string, mixed>|null
     */
    public function attributes(): ?array
    {
        return $this->data['attributes'] ?? null;
    }

    /**
     * Event creation date.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this->data['created_at']);
    }
}
