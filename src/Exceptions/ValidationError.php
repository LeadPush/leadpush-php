<?php

declare(strict_types=1);

namespace Leadpush\SDK\Exceptions;

/**
 * Error thrown when the API rejects request validation.
 */
class ValidationError extends ApiError
{
    /**
     * Create a validation API error.
     */
    public function __construct(mixed $response = null, ?\Throwable $previous = null)
    {
        parent::__construct(422, $response, previous: $previous);
    }
}
