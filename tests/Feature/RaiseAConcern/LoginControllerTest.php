<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

it('shows the login page for guests', function () {
    $this->get('/login')
        ->assertSuccessful()
        ->assertViewIs('auth.login');
});

it('redirects an authenticated user away from the login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirectToRoute('raiseaconcern.submit');
});

it('redirects to Firefly for the senior school', function () {
    $response = $this->get('/login/firefly/senior');

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('cranleigh.fireflycloud.net');
});

it('redirects to Firefly for the prep school', function () {
    $response = $this->get('/login/firefly/prep');

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('cranprep.fireflycloud.net');
});

it('returns 404 for an invalid school name', function () {
    $this->get('/login/firefly/unknown')->assertNotFound();
});

it('validates that ffauth_secret is required for the callback', function () {
    $this->get('/login/firefly/senior/success')
        ->assertRedirect();
});

it('creates a user and logs them in on a successful Firefly callback', function () {
    Http::fake([
        'https://cranleigh.fireflycloud.net/*' => Http::response(fireflyXmlResponse(
            email: 'staff@cranleigh.org',
            name: 'Jane Staff',
            username: 'JSTAFF',
            ssoType: 'staff',
            ssoId: 99
        )),
    ]);

    $this->get('/login/firefly/senior/success?ffauth_secret=abc123')
        ->assertRedirect();

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'staff@cranleigh.org',
        'sso_type' => 'staff',
    ]);
});

it('logs in an existing user without creating a duplicate on callback', function () {
    $existingUser = User::factory()->staff()->create(['email' => 'staff@cranleigh.org']);

    Http::fake([
        'https://cranleigh.fireflycloud.net/*' => Http::response(fireflyXmlResponse(
            email: 'staff@cranleigh.org',
            ssoType: 'staff',
            ssoId: $existingUser->sso_id
        )),
    ]);

    $this->get('/login/firefly/senior/success?ffauth_secret=abc123')
        ->assertRedirect();

    $this->assertAuthenticatedAs($existingUser);
    $this->assertDatabaseCount('users', 1);
});

it('logs out an authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect();

    $this->assertGuest();
});
