# leadpush/sdk-php

Official PHP SDK for the Leadpush API.

Create a Leadpush account at [leadpush.io](https://leadpush.io).

## Installation

```sh
composer require leadpush/sdk-php
```

## Requirements

- PHP 8.2 or newer
- A Leadpush API key

## Quick Start

```php
<?php

use Leadpush\SDK\Leadpush;

$client = new Leadpush($_ENV['LEADPUSH_API_KEY']);

$contacts = $client->contacts()->list([
    'page' => 1,
    'per_page' => 10,
]);

print_r($contacts->data());
```

## Configuration

```php
<?php

use Leadpush\SDK\Leadpush;

$client = new Leadpush('leadpush_api_key', [
    'baseUrl' => 'https://api.leadpush.io/v1',
    'timeout' => 30000,
    'headers' => [
        'X-App-Name' => 'my-app',
    ],
]);
```

Defaults:

- `baseUrl`: `https://api.leadpush.io/v1`
- `timeout`: `30000`

You can pass a Symfony `HttpClientInterface` as the third constructor argument for custom transports or tests.

## Contacts

Contact methods that accept a contact identifier can use either the contact uuid or the workspace identity field value, such as an email address.

**Get A Contact**

```php
$contact = $client->contacts()->get('contact_uuid');
$sameContact = $client->contacts()->get('person@example.com');

echo $contact->uuid();
echo $contact->attributes()['email'];
```

**Create A Contact**

```php
$contact = $client->contacts()->create([
    'subscribed' => true,
    'attributes' => [
        'email' => 'person@example.com',
        'first_name' => 'Person',
    ],
]);
```

**Update A Contact**

```php
$contact = $client->contacts()->update('contact_uuid', [
    'subscribed' => false,
    'attributes' => [
        'first_name' => 'Updated',
    ],
]);

$client->contacts()->update('person@example.com', [
    'subscribed' => true,
]);
```

**Update From A Model**

```php
$contact = $client->contacts()->get('contact_uuid');

$contact->setSubscribed(false);
$contact->setAttribute('first_name', 'Updated');

$contact->update();
```

**Subscribe Or Unsubscribe**

```php
$client->contacts()->subscribe('person@example.com');
$client->contacts()->unsubscribe('person@example.com');

$contact->subscribe();
$contact->unsubscribe();
```

**Contact Events**

```php
$events = $client->contacts()->events('contact_uuid')->list([
    'search' => 'purchase',
]);

$sameEvents = $client->contacts()->events('person@example.com')->list();
```

You can also access events from an attached contact model:

```php
$contact = $client->contacts()->get('contact_uuid');
$events = $contact->events()->list();
```

**Create A Contact Event**

```php
$client->contacts()->events('contact_uuid')->create([
    'event_name' => 'purchase',
    'attributes' => [
        'plan' => 'enterprise',
    ],
]);

$client->contacts()->events('person@example.com')->create([
    'event_name' => 'purchase',
]);
```

Contact event creation returns `null` when the API accepts the event. The create endpoint does not return the created event.

## Pagination

**List One Page**

```php
$page = $client->contacts()->list([
    'page' => 1,
    'per_page' => 25,
]);

print_r($page->data());
echo $page->meta()->hasNext() ? 'more' : 'done';
```

**Iterate Every Model**

```php
foreach ($client->contacts()->listAll(['per_page' => 100]) as $contact) {
    echo $contact->uuid();
}
```

**Iterate Page By Page**

```php
foreach ($client->contacts()->cursor(['per_page' => 100]) as $page) {
    echo $page->meta()->currentPage();
    echo count($page->data());
}
```

## Fields

**List Fields**

```php
$fields = $client->fields()->list([
    'search' => 'company',
    'filters' => [
        [
            'id' => 'type',
            'value' => ['text'],
        ],
    ],
]);
```

**Create A Field**

```php
$field = $client->fields()->create([
    'name' => 'company_name',
    'type' => 'text',
    'format' => [
        'text' => 'url',
    ],
]);
```

## Suppressions

**List Suppressions**

```php
$suppressions = $client->suppressions()->list([
    'search' => 'blocked@example.com',
    'filters' => [
        [
            'id' => 'type',
            'value' => ['manual'],
        ],
    ],
]);
```

**Create A Suppression**

```php
$suppression = $client->suppressions()->create([
    'email' => 'blocked@example.com',
    'type' => 'manual',
]);
```

Suppressions do not support updates. Calling `$client->suppressions()->update(...)` throws `UnsupportedEndpointError`.

## Low-Level Requests

Use `get`, `post`, or `delete` for endpoints that do not have a typed resource yet.

**GET**

```php
$response = $client->get('contacts/contact_uuid/events');
```

**POST**

```php
$response = $client->post('contacts/contact_uuid/subscribe');
```

**DELETE**

```php
$client->delete('contacts/contact_uuid');
```

Paths can also be passed as arrays:

```php
$client->get(['contacts', 'contact_uuid', 'events']);
```

## Errors

The SDK throws typed errors for common API failures:

```php
<?php

use Leadpush\SDK\Exceptions\UnauthorizedError;
use Leadpush\SDK\Exceptions\ValidationError;

try {
    $client->contacts()->list();
} catch (UnauthorizedError) {
    echo 'Invalid API key';
} catch (ValidationError $error) {
    print_r($error->response());
}
```

Available errors:

- `ApiError`
- `UnauthorizedError`
- `ForbiddenError`
- `NotFoundError`
- `ValidationError`
- `TimeoutError`
- `UnsupportedEndpointError`

## Development

```sh
composer install
composer test
```

## Releasing

Releases are created from Git tags. Packagist reads the GitHub repository and publishes Composer versions from tags like `v1.0.0`.

Before the first release, submit this repository to Packagist as `leadpush/sdk-php` and enable the Packagist GitHub integration so new tags update automatically.

To publish a release:

1. Open the `Release` workflow in GitHub Actions.
2. Run it manually with a SemVer tag like `v1.0.0`.
3. Wait for the matrix tests to pass.

The workflow creates and pushes the Git tag, then creates a GitHub Release. Packagist will expose the tag as the matching Composer version.

## License

MIT
