<?php

declare(strict_types=1);

use Leadpush\SDK\Models\EmailSendModel;

use function Leadpush\SDK\Test\Support\createClient;
use function Leadpush\SDK\Test\Support\createEmailData;
use function Leadpush\SDK\Test\Support\emailSendData;
use function Leadpush\SDK\Test\Support\expectedHeaderLines;
use function Leadpush\SDK\Test\Support\jsonResponse;
use function Leadpush\SDK\Test\Support\requestHeaders;
use function Leadpush\SDK\Test\Support\testBaseUrl;

it('sends an email', function () {
    $response = jsonResponse([
        'data' => emailSendData(),
    ], 202);
    [$client] = createClient([$response]);

    $send = $client->emails()->send(createEmailData());

    expect($send)->toBeInstanceOf(EmailSendModel::class)
        ->and($send->isAccepted())->toBeTrue()
        ->and($send->messageCount())->toBe(4)
        ->and($send->messages()[0]['uuid'])->toBe(emailSendData()['messages'][0]['uuid'])
        ->and($send->messages()[0]['recipient'])->toBe('known@example.test')
        ->and($send->messages()[0]['type'])->toBe('to')
        ->and($send->messages()[3]['type'])->toBe('bcc')
        ->and($response->getRequestMethod())->toBe('POST')
        ->and($response->getRequestUrl())->toBe(testBaseUrl() . '/emails')
        ->and($response->getRequestOptions()['body'])->toBe(json_encode(createEmailData(), JSON_THROW_ON_ERROR))
        ->and(requestHeaders($response))->toContain(...expectedHeaderLines([
            'Content-Type' => 'application/json',
        ]));
});
