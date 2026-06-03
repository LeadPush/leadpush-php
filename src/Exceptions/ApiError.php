<?php

declare(strict_types=1);

namespace Leadpush\SDK\Exceptions;

/**
 * Error thrown when the Leadpush API returns a non-success status.
 */
class ApiError extends LeadpushError
{
    /**
     * Create an API error.
     */
    public function __construct(int $status, mixed $response = null, ?string $message = null, ?\Throwable $previous = null)
    {
        parent::__construct($message ?? "Leadpush API request failed with status {$status}.", $status, $response, $previous);
    }
}
