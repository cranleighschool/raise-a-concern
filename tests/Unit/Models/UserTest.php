<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

it('identifies staff users correctly', function () {
    $user = User::factory()->staff()->make();
    expect($user->isStaff())->toBeTrue()
        ->and($user->isPupil())->toBeFalse()
        ->and($user->isParent())->toBeFalse();
});

it('identifies pupil users correctly', function () {
    $user = User::factory()->pupil()->make();
    expect($user->isPupil())->toBeTrue()
        ->and($user->isStaff())->toBeFalse()
        ->and($user->isParent())->toBeFalse();
});

it('identifies parent users correctly', function () {
    $user = User::factory()->asParent()->make();
    expect($user->isParent())->toBeTrue()
        ->and($user->isStaff())->toBeFalse()
        ->and($user->isPupil())->toBeFalse();
});

it('returns false for all role checks when sso_type is null', function () {
    $user = User::factory()->make(['sso_type' => null]);
    expect($user->isStaff())->toBeFalse()
        ->and($user->isPupil())->toBeFalse()
        ->and($user->isParent())->toBeFalse();
});

it('returns an empty collection for getPupilsOfParent when the user is not a parent', function (string $state) {
    $user = User::factory()->{$state}()->make();
    expect($user->getPupilsOfParent())->toBeEmpty();
})->with(['staff', 'pupil']);

it('calls the pastoral module API to fetch pupils for a parent user', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response([
            ['pupil_id' => 1001, 'name' => 'Alice Smith', 'ncYear' => 11],
            ['pupil_id' => 1002, 'name' => 'Bob Smith', 'ncYear' => 9],
        ]),
    ]);

    $parent = User::factory()->asParent()->make(['email' => 'parent@example.com']);
    $pupils = $parent->getPupilsOfParent();

    expect($pupils)->toHaveCount(2);
    Http::assertSent(fn ($request) => str_contains($request->url(), 'selfreflections/parents/pupils'));
});
