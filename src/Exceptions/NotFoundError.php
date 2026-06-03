<?php

declare(strict_types=1);

namespace Leadpush\SDK\Exceptions;

/**
 * Error thrown when the requested resource is not found.
 */
class NotFoundError extends ApiError
{
    /**
     * Create a not-found API error.
     */
    public function __construct(mixed $response = null, ?\Throwable $previous = null)
    {
        parent::__construct(404, $response, previous: $previous);
    }
}
