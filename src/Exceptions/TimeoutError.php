<?php

declare(strict_types=1);

namespace Leadpush\SDK\Exceptions;

/**
 * Error thrown when a Leadpush API request times out.
 */
class TimeoutError extends LeadpushError
{
    /**
     * Create a request timeout error.
     */
    public function __construct(int $timeout, ?\Throwable $previous = null)
    {
        parent::__construct("Leadpush API request timed out after {$timeout}ms.", previous: $previous);
    }
}
