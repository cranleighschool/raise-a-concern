<?php

use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

if (! function_exists('displayAlertMsg')) {
    function displayAlertMsg(): string
    {
        $alerts = [];
        foreach (['danger', 'warning', 'success', 'info'] as $alert) {
            if (Session::has('alert-'.$alert)) {
                $msg = Session::get('alert-'.$alert);
                $alerts[] = sprintf('<div class="alert alert-%s">%s</div>', $alert, $msg);
            }
        }

        if (Session::has('message')) {
            $alerts[] = sprintf('<div class="alert alert-info">%s</div>', Session::get('message'));
        }
        if ($alerts) {
            echo sprintf('%s', implode('', $alerts));
        }

        return '';
    }
}
if(!function_exists('csp_nonce')) {
    function csp_nonce(): string
    {
        if (!app()->bound('csp-nonce')) {
            return '';
        }
        return app('csp-nonce');
    }
}
if (! function_exists('getRealIpAddress')) {
    function getRealIpAddress()
    {
        //whether ip is from share internet
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } //whether ip is from proxy
        elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } //whether ip is from remote address
        else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        return $ip_address;
    }

}

if (! function_exists('getAppVersion')) {
    function getAppVersion(): string
    {
        return Cache::remember('githubReleaseVersion', now()->addWeek(), function (): string {
            try {
                $response = Http::withToken(config('services.github.key'))
                    ->get('https://api.github.com/repos/cranleighschool/raise-a-concern/releases')
                    ->throw()
                    ->collect()
                    ->first();

                return $response['tag_name'].' - '.Carbon::parse($response['published_at'])->format('Y-m-d H:i:s');
            } catch (RequestException $exception) {
                if ($exception->getCode() === 401) {
                    Log::error('Github API token seems to be dead. #sadface');

                    // TODO: send notification to admins
                }
            } catch (ConnectionException $exception) {
                Log::error('Could not connect to github');

                // TODO: send a notification to admins
            }

            return 'Unknown';
        });
    }
}
