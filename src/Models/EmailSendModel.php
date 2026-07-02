<?php

declare(strict_types=1);

namespace Leadpush\SDK\Models;

use Leadpush\SDK\Model;

/**
 * Accepted email send returned by the Leadpush API.
 */
class EmailSendModel extends Model
{
    /**
     * Whether the email send was accepted for delivery.
     */
    public function isAccepted(): bool
    {
        return (bool) $this->data['accepted'];
    }

    /**
     * Number of per-recipient messages created.
     */
    public function messageCount(): int
    {
        return (int) $this->data['message_count'];
    }

    /**
     * Per-recipient messages created for this send.
     *
     * @return array<int, array{uuid: string, recipient: string, type: string, from: string, status: string}>
     */
    public function messages(): array
    {
        return $this->data['messages'] ?? [];
    }
}
