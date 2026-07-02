<?php

declare(strict_types=1);

namespace Leadpush\SDK\Models;

use Leadpush\SDK\Model;
use Leadpush\SDK\Resources\DomainAddresses;

/**
 * Domain returned by the Leadpush API.
 */
class DomainModel extends Model
{
    /**
     * Domain id.
     */
    public function uuid(): string
    {
        return (string) $this->data['uuid'];
    }

    /**
     * Domain name.
     */
    public function name(): string
    {
        return (string) $this->data['name'];
    }

    /**
     * Domain name.
     */
    public function domain(): string
    {
        return (string) $this->data['domain'];
    }

    /**
     * Whether the domain is verified.
     */
    public function isVerified(): bool
    {
        return (bool) $this->data['verified'];
    }

    /**
     * Domain provider.
     */
    public function provider(): string
    {
        return (string) $this->data['provider'];
    }

    /**
     * Domain lifecycle status.
     */
    public function status(): string
    {
        return (string) $this->data['status'];
    }

    /**
     * Domain verification status.
     */
    public function verification(): string
    {
        return (string) $this->data['verification'];
    }

    /**
     * Custom MAIL FROM domain.
     */
    public function mailFromDomain(): string
    {
        return (string) $this->data['mail_from_domain'];
    }

    /**
     * Whether the custom MAIL FROM domain is verified.
     */
    public function isMailFromVerified(): bool
    {
        return (bool) $this->data['mail_from_verified'];
    }

    /**
     * DNS records required for verification.
     *
     * @return array<int, array{type: string, name: string, value: string, is_valid: bool}>
     */
    public function dns(): array
    {
        return $this->data['dns'] ?? [];
    }

    /**
     * Domain creation date.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this->data['created_at']);
    }

    /**
     * Domain last update date.
     */
    public function updatedAt(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this->data['updated_at']);
    }

    /**
     * Refresh domain verification status.
     */
    public function verify(): self
    {
        $payload = $this->post([$this->uuid(), 'verification']);
        $this->replaceData($payload['data']);
        $this->clearDirty();

        return $this;
    }

    /**
     * Delete this domain.
     */
    public function delete(): null
    {
        $this->requestDelete([$this->uuid()]);

        return null;
    }

    /**
     * Access address API operations for this domain.
     */
    public function addresses(): DomainAddresses
    {
        return new DomainAddresses($this->requireContext()->client(), $this->uuid());
    }
}
