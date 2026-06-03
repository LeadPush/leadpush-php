<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Leadpush;
use Leadpush\SDK\ListableResource;
use Leadpush\SDK\Models\ContactEventModel;

/**
 * Contact events API resource.
 */
class ContactEvents extends ListableResource
{
    /**
     * Create a contact events resource handler.
     */
    public function __construct(Leadpush $client, private readonly string $contactId)
    {
        parent::__construct($client);
    }

    /**
     * Create a contact event.
     *
     * @param array{event_name: string, attributes?: array<string, mixed>} $data Contact event creation payload.
     */
    public function create(array $data): null
    {
        $request = [
            'event_name' => $data['event_name'],
        ];

        if (array_key_exists('attributes', $data)) {
            $request['attributes'] = json_encode($data['attributes'], JSON_THROW_ON_ERROR);
        }

        $this->postResource(null, $request);

        return null;
    }

    /**
     * Return the API path for contact events.
     *
     * @return array<int, string>
     */
    protected function endpoint(): string|array
    {
        return ['contacts', $this->contactId, 'events'];
    }

    /**
     * Return the model class used to wrap contact event data.
     *
     * @return class-string<ContactEventModel>
     */
    protected function modelClass(): string
    {
        return ContactEventModel::class;
    }
}
