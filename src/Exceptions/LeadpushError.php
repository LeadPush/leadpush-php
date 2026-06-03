<?php

declare(strict_types=1);

namespace Leadpush\SDK\Exceptions;

/**
 * Base error thrown by the Leadpush SDK.
 */
class LeadpushError extends \RuntimeException
{
    /**
     * Create an SDK error.
     */
    public function __construct(
        string $message,
        private readonly ?int $status = null,
        private readonly mixed $response = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * HTTP status code returned by the API, when available.
     */
    public function status(): ?int
    {
        return $this->status;
    }

    /**
     * Parsed API response body, when available.
     */
    public function response(): mixed
    {
        return $this->response;
    }
}
