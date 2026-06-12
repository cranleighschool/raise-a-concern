<?php

use App\Models\User;

it('creates a new user record via the static create method', function () {
    $user = User::create(
        email: 'new@example.com',
        ssoType: 'staff',
        name: 'New User',
        username: 'NEWUSER',
        ssoId: 99999
    );

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->email)->toBe('new@example.com')
        ->and($user->sso_type)->toBe('staff')
        ->and($user->name)->toBe('New User')
        ->and($user->username)->toBe('NEWUSER');

    $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
});

it('returns the existing user when called with a duplicate email', function () {
    $existing = User::factory()->staff()->create(['email' => 'existing@example.com']);

    $returned = User::create(
        email: 'existing@example.com',
        ssoType: 'staff',
        name: 'Duplicate',
        username: 'DUP',
        ssoId: 1
    );

    expect($returned->id)->toBe($existing->id);
    $this->assertDatabaseCount('users', 1);
});
