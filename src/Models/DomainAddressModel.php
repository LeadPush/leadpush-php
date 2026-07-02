<?php

declare(strict_types=1);

namespace Leadpush\SDK\Models;

use Leadpush\SDK\Model;

/**
 * Domain address returned by the Leadpush API.
 */
class DomainAddressModel extends Model
{
    /**
     * Domain address id.
     */
    public function uuid(): string
    {
        return (string) $this->data['uuid'];
    }

    /**
     * Parent domain id.
     */
    public function domainUuid(): string
    {
        return (string) $this->data['domain_uuid'];
    }

    /**
     * Address local part.
     */
    public function address(): string
    {
        return (string) $this->data['address'];
    }

    /**
     * Full email address.
     */
    public function fullAddress(): string
    {
        return (string) $this->data['full_address'];
    }

    /**
     * Domain provider.
     */
    public function provider(): ?string
    {
        return $this->data['provider'] ?? null;
    }

    /**
     * Sender display name.
     */
    public function displayName(): string
    {
        return (string) $this->data['display_name'];
    }

    /**
     * Address verification status.
     */
    public function verification(): string
    {
        return (string) $this->data['verification'];
    }

    /**
     * Address creation date.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this->data['created_at']);
    }

    /**
     * Address last update date.
     */
    public function updatedAt(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this->data['updated_at']);
    }

    /**
     * Delete this domain address.
     */
    public function delete(): null
    {
        $this->requestDelete([$this->uuid()]);

        return null;
    }
}
