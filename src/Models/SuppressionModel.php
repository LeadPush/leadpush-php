<?php

declare(strict_types=1);

namespace Leadpush\SDK\Models;

use Leadpush\SDK\Model;

/**
 * Suppression returned by the Leadpush API.
 */
class SuppressionModel extends Model
{
    /**
     * Suppression id.
     */
    public function uuid(): string
    {
        return (string) $this->data['uuid'];
    }

    /**
     * Suppressed email address.
     */
    public function email(): string
    {
        return (string) $this->data['email'];
    }

    /**
     * Suppression type.
     */
    public function type(): string
    {
        return (string) $this->data['type'];
    }

    /**
     * Suppression creation date.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this->data['created_at']);
    }
}
