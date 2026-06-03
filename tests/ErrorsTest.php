<?php

declare(strict_types=1);

use Leadpush\SDK\Exceptions\UnauthorizedError;

use function Leadpush\SDK\Test\Support\createClient;
use function Leadpush\SDK\Test\Support\jsonResponse;

it('throws an unauthorized error for 401 responses', function () {
    $payload = [
        'message' => 'Unauthenticated.',
    ];
    [$client] = createClient([jsonResponse($payload, 401)], key: 'bad-key');

    try {
        $client->contacts()->list();
        throw new RuntimeException('Expected contacts list to throw.');
    } catch (UnauthorizedError $error) {
        expect($error->status())->toBe(401)
            ->and($error->response())->toBe($payload)
            ->and($error->getMessage())->toBe('Unauthorized. Check your Leadpush API key.');
    }
});
