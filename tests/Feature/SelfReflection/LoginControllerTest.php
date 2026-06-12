<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.firefly.selfreflections.url', 'https://firefly.example.com');
    config()->set('services.firefly.selfreflections.app', 'selfreflections-test');
    config()->set('services.isams.batch_api_key', 'test-key');
    config()->set('services.isams.batch_api_url', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx');
});

it('redirects to Firefly for login', function () {
    $response = $this->get(selfReflectionUrl('/login'));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('firefly.example.com');
});

it('includes the app name and success/failure URLs in the Firefly redirect', function () {
    $response = $this->get(selfReflectionUrl('/login'));

    $location = $response->headers->get('Location');
    expect($location)
        ->toContain('selfreflections-test')
        ->toContain('successURL')
        ->toContain('failURL');
});

it('flashes an error and redirects back on callback failure', function () {
    $response = $this->from(selfReflectionUrl('/login'))
        ->get(selfReflectionUrl('/login/callback/failure'));

    $response->assertRedirect();
    $this->assertGuest();
});

it('creates a pupil user and logs them in on a successful callback', function () {
    Http::fake([
        'https://firefly.example.com/*' => Http::response(fireflyXmlResponse(
            email: 'pupil@cranleigh.org',
            name: 'John Smith',
            username: 'jsmith',
            ssoType: 'stu',
            ssoId: 42
        )),
    ]);

    $this->get(selfReflectionUrl('/login/callback/success?ffauth_secret=abc123'))
        ->assertRedirect();

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'pupil@cranleigh.org',
        'sso_type' => 'stu',
    ]);
});

it('validates that ffauth_secret is required on the callback', function () {
    $this->get(selfReflectionUrl('/login/callback/success'))
        ->assertRedirect();

    $this->assertGuest();
});

it('logs out an authenticated user', function () {
    $user = User::factory()->pupil()->create();

    $this->actingAs($user)
        ->post(selfReflectionUrl('/logout'))
        ->assertRedirect();

    $this->assertGuest();
});

it('allows FRB to impersonate a pupil', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $frb = User::factory()->staff()->create(['username' => 'FRB']);
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    $this->actingAs($frb)
        ->get(selfReflectionUrl('/impersonate/jsmith'))
        ->assertRedirectToRoute('selfreflection.home');

    $this->assertAuthenticatedAs($pupil);
});

it('denies impersonation for non-FRB users', function () {
    $staff = User::factory()->staff()->create(['username' => 'OTHER']);
    User::factory()->pupil()->create(['username' => 'jsmith']);

    $this->actingAs($staff)
        ->get(selfReflectionUrl('/impersonate/jsmith'))
        ->assertForbidden();
});

it('returns 404 when impersonating a non-existent pupil', function () {
    $frb = User::factory()->staff()->create(['username' => 'FRB']);

    $this->actingAs($frb)
        ->get(selfReflectionUrl('/impersonate/doesnotexist'))
        ->assertNotFound();
});
