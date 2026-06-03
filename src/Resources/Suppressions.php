<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Entity;
use Leadpush\SDK\Exceptions\UnsupportedEndpointError;
use Leadpush\SDK\Model;
use Leadpush\SDK\Models\SuppressionModel;

/**
 * Suppression API resource.
 */
class Suppressions extends Entity
{
    /**
     * Return the API path segment for suppressions.
     */
    protected function endpoint(): string|array
    {
        return 'suppressions';
    }

    /**
     * Return the model class used to wrap suppression data.
     *
     * @return class-string<SuppressionModel>
     */
    protected function modelClass(): string
    {
        return SuppressionModel::class;
    }

    /**
     * Suppressions do not support updates.
     *
     * @param array<string, mixed> $data
     */
    public function update(string $id, array $data): Model
    {
        throw new UnsupportedEndpointError('The suppressions update endpoint is not supported.');
    }
}
