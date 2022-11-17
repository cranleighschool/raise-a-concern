<?php

if (! function_exists('displayAlertMsg')) {
    /**
     * @return string
     */
    function displayAlertMsg(): string
    {
        $alerts = [];
        foreach (['danger', 'warning', 'success', 'info'] as $alert) {
            if (Session::has('alert-'.$alert)) {
                $msg = Session::get('alert-'.$alert);
                array_push($alerts, sprintf('<div class="alert alert-%s">%s</div>', $alert, $msg));
            }
        }

        if (Session::has('message')) {
            array_push($alerts, sprintf('<div class="alert alert-info">%s</div>', Session::get('message')));
        }
        if ($alerts) {
            echo sprintf('%s', implode('', $alerts));
        }

        return '';
    }
}
if (!function_exists('getRealIpAddress')) {
    function getRealIpAddress()
    {
        //whether ip is from share internet
        if (! empty($_SERVER[ 'HTTP_CLIENT_IP' ])) {
            $ip_address = $_SERVER[ 'HTTP_CLIENT_IP' ];
        } //whether ip is from proxy
        elseif (! empty($_SERVER[ 'HTTP_X_FORWARDED_FOR' ])) {
            $ip_address = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
        } //whether ip is from remote address
        else {
            $ip_address = $_SERVER[ 'REMOTE_ADDR' ];
        }
        return $ip_address;
    }
}
