<?php

declare(strict_types=1);

namespace Leadpush\SDK\Exceptions;

/**
 * Error thrown when the API rejects the request as forbidden.
 */
class ForbiddenError extends ApiError
{
    /**
     * Create a forbidden API error.
     */
    public function __construct(mixed $response = null, ?\Throwable $previous = null)
    {
        parent::__construct(403, $response, previous: $previous);
    }
}
