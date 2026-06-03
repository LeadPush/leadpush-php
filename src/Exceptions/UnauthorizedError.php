<?php

declare(strict_types=1);

namespace Leadpush\SDK\Exceptions;

/**
 * Error thrown when the API rejects the request as unauthenticated.
 */
class UnauthorizedError extends ApiError
{
    /**
     * Create an unauthorized API error.
     */
    public function __construct(mixed $response = null, ?\Throwable $previous = null)
    {
        parent::__construct(401, $response, 'Unauthorized. Check your Leadpush API key.', $previous);
    }
}
