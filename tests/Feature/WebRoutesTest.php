<?php

use Illuminate\Support\Facades\Notification;

it('returns a successful response for the health endpoint', function () {
    $this->get('/health')->assertSuccessful();
});

it('returns a JSON response for the health.json endpoint', function () {
    Notification::fake();

    $this->get('/health.json')->assertSuccessful()->assertJsonIsObject();
});

it('accepts a POST to the CSP report endpoint and returns no content', function () {
    $payload = json_encode([
        'csp-report' => [
            'document-uri' => 'https://example.com',
            'violated-directive' => 'script-src',
        ],
    ]);

    $this->call('POST', '/csp-report', [], [], [], ['CONTENT_TYPE' => 'application/csp-report'], $payload)
        ->assertNoContent();
});

it('does not set security headers outside of the production environment', function () {
    $response = $this->get('/login');

    $response->assertHeaderMissing('X-XSS-Protection');
    $response->assertHeaderMissing('Permissions-Policy');
});
