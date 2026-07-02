<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\ListableResource;
use Leadpush\SDK\Models\DomainModel;

/**
 * Domain API resource.
 */
class Domains extends ListableResource
{
    /**
     * Return the API path segment for domains.
     */
    protected function endpoint(): string|array
    {
        return 'domains';
    }

    /**
     * Return the model class used to wrap domain data.
     *
     * @return class-string<DomainModel>
     */
    protected function modelClass(): string
    {
        return DomainModel::class;
    }

    /**
     * Create a domain.
     *
     * @param array{name: string, dkim_selectors?: array<int, string>|null, tracking_subdomain?: string|null, tracking_mode?: string|null} $data Domain creation payload.
     */
    public function create(array $data): DomainModel
    {
        $payload = $this->postResource(null, $data);

        return $this->makeModel($payload['data']);
    }

    /**
     * Get a domain by uuid.
     */
    public function get(string $uuid): DomainModel
    {
        $payload = $this->getResource([$uuid]);

        return $this->makeModel($payload['data']);
    }

    /**
     * Delete a domain by uuid.
     */
    public function delete(string $uuid): null
    {
        $this->deleteResource([$uuid]);

        return null;
    }

    /**
     * Refresh domain verification status.
     */
    public function verify(string $uuid): DomainModel
    {
        $payload = $this->postResource([$uuid, 'verification']);

        return $this->makeModel($payload['data']);
    }

    /**
     * Access address API operations for a domain.
     */
    public function addresses(string $uuid): DomainAddresses
    {
        return new DomainAddresses($this->client, $uuid);
    }
}
