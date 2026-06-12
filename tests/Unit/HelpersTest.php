<?php

it('getRealIpAddress returns HTTP_CLIENT_IP when set', function () {
    $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.1';
    unset($_SERVER['HTTP_X_FORWARDED_FOR']);

    expect(getRealIpAddress())->toBe('192.168.1.1');
})->after(function () {
    unset($_SERVER['HTTP_CLIENT_IP']);
});

it('getRealIpAddress returns HTTP_X_FORWARDED_FOR when CLIENT_IP is absent', function () {
    unset($_SERVER['HTTP_CLIENT_IP']);
    $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.1';

    expect(getRealIpAddress())->toBe('10.0.0.1');
})->after(function () {
    unset($_SERVER['HTTP_X_FORWARDED_FOR']);
});

it('getRealIpAddress falls back to REMOTE_ADDR', function () {
    unset($_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_X_FORWARDED_FOR']);
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    expect(getRealIpAddress())->toBe('127.0.0.1');
});

it('csp_nonce returns an empty string when not bound', function () {
    expect(csp_nonce())->toBe('');
});

it('csp_nonce returns the nonce when bound in the container', function () {
    app()->bind('csp-nonce', fn () => 'abc123nonce');

    expect(csp_nonce())->toBe('abc123nonce');
});

it('displayAlertMsg renders a danger alert from the session', function () {
    session()->flash('alert-danger', 'Something went wrong');

    ob_start();
    displayAlertMsg();
    $output = ob_get_clean();

    expect($output)->toContain('alert-danger')->toContain('Something went wrong');
});

it('displayAlertMsg renders all alert types from the session', function (string $type) {
    session()->flash("alert-{$type}", "A {$type} message");

    ob_start();
    displayAlertMsg();
    $output = ob_get_clean();

    expect($output)->toContain("alert-{$type}")->toContain("A {$type} message");
})->with(['danger', 'warning', 'success', 'info']);

it('displayAlertMsg renders a generic message from the session', function () {
    session()->flash('message', 'A generic message here');

    ob_start();
    displayAlertMsg();
    $output = ob_get_clean();

    expect($output)->toContain('alert-info')->toContain('A generic message here');
});

it('displayAlertMsg returns an empty string when there are no alerts', function () {
    expect(displayAlertMsg())->toBe('');
});
