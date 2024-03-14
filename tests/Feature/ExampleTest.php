<?php

it('returns a successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

it('can view submit page without logging in', function () {
    $host = config('app.domains.raiseaconcern.url');
    $response = $this->get($host.'/submit');

    $response->assertStatus(200);
});
