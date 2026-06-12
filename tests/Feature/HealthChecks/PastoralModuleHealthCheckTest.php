<?php

use App\Http\HealthChecks\PastoralModuleApiConnectionCheck;
use Illuminate\Support\Facades\Http;

it('returns ok when the expected user is accessible and enabled', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response([
            ['username' => 'RAISEACONCERNAPP', 'enabled' => true],
        ]),
    ]);

    $result = (new PastoralModuleApiConnectionCheck)->run();

    expect($result->status->value)->toBe('ok');
});

it('returns failed when the API user list is empty', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response([]),
    ]);

    $result = (new PastoralModuleApiConnectionCheck)->run();

    expect($result->status->value)->toBe('failed');
});

it('returns warning when the API returns the wrong user', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response([
            ['username' => 'WRONGUSER', 'enabled' => true],
        ]),
    ]);

    $result = (new PastoralModuleApiConnectionCheck)->run();

    expect($result->status->value)->toBe('warning');
});

it('returns failed when the API throws an exception', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response('Server Error', 500),
    ]);

    $result = (new PastoralModuleApiConnectionCheck)->run();

    expect($result->status->value)->toBe('failed');
});
