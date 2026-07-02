<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Models\EmailSendModel;
use Leadpush\SDK\Resource;

/**
 * Email sending API resource.
 */
class Emails extends Resource
{
    /**
     * Queue an email for delivery.
     *
     * @param array{from: string, subject: string, html?: string, text?: string, to?: array<int, string>, bcc?: array<int, string>, reply_to?: string, headers?: array<string, string>} $data Email send payload.
     */
    public function send(array $data): EmailSendModel
    {
        $payload = $this->postResource(null, $data);

        return $this->makeModel($payload['data']);
    }

    /**
     * Return the API path segment for email sends.
     */
    protected function endpoint(): string|array
    {
        return 'emails';
    }

    /**
     * Return the model class used to wrap email send response data.
     *
     * @return class-string<EmailSendModel>
     */
    protected function modelClass(): string
    {
        return EmailSendModel::class;
    }
}
