<?php

declare(strict_types=1);

namespace Leadpush\SDK\Models;

use Leadpush\SDK\Model;

/**
 * Custom contact field returned by the Leadpush API.
 */
class FieldModel extends Model
{
    /**
     * Field id.
     */
    public function uuid(): string
    {
        return (string) $this->data['uuid'];
    }

    /**
     * Field name.
     */
    public function name(): string
    {
        return (string) $this->data['name'];
    }

    /**
     * Field data type.
     */
    public function type(): string
    {
        return (string) $this->data['type'];
    }

    /**
     * Field format configuration.
     *
     * @return array{text?: string|null, pattern?: string|null, iso_format?: string|null}|null
     */
    public function format(): ?array
    {
        return $this->data['format'] ?? null;
    }

    /**
     * Field creation date.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this->data['created_at']);
    }
}
