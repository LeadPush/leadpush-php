<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Entity;
use Leadpush\SDK\Models\FieldModel;

/**
 * Field API resource.
 */
class Fields extends Entity
{
    /**
     * Return the API path segment for fields.
     */
    protected function endpoint(): string|array
    {
        return 'fields';
    }

    /**
     * Return the model class used to wrap field data.
     *
     * @return class-string<FieldModel>
     */
    protected function modelClass(): string
    {
        return FieldModel::class;
    }
}
