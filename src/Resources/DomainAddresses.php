<?php

declare(strict_types=1);

namespace Leadpush\SDK\Resources;

use Leadpush\SDK\Leadpush;
use Leadpush\SDK\ListableResource;
use Leadpush\SDK\Models\DomainAddressModel;

/**
 * Domain addresses API resource.
 */
class DomainAddresses extends ListableResource
{
    /**
     * Create a domain addresses resource handler.
     *
     * @param string $domainUuid Parent domain uuid.
     */
    public function __construct(Leadpush $client, private readonly string $domainUuid)
    {
        parent::__construct($client);
    }

    /**
     * Create a domain address.
     *
     * @param array{address: string, display_name: string, reply_to: string, company_address: string, company_address_2?: string|null, company_city: string, company_state: string, company_zip: string, company_country: string} $data Domain address creation payload.
     */
    public function create(array $data): DomainAddressModel
    {
        $payload = $this->postResource(null, $data);

        return $this->makeModel($payload['data']);
    }

    /**
     * Get a domain address by uuid.
     */
    public function get(string $uuid): DomainAddressModel
    {
        $payload = $this->getResource([$uuid]);

        return $this->makeModel($payload['data']);
    }

    /**
     * Delete a domain address by uuid.
     */
    public function delete(string $uuid): null
    {
        $this->deleteResource([$uuid]);

        return null;
    }

    /**
     * Return the API path for domain addresses.
     *
     * @return array<int, string>
     */
    protected function endpoint(): string|array
    {
        return ['domains', $this->domainUuid, 'addresses'];
    }

    /**
     * Return the model class used to wrap domain address data.
     *
     * @return class-string<DomainAddressModel>
     */
    protected function modelClass(): string
    {
        return DomainAddressModel::class;
    }
}
