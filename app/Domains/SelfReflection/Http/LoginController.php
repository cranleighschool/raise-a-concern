<?php

namespace App\Domains\SelfReflection\Http;

use App\Http\Controllers\Auth\FireflyAuth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController
{
    use FireflyAuth;

    public string $url;

    public function __construct()
    {
        $this->url = config('services.firefly.selfreflections.url');
    }

    public function redirectTo(): RedirectResponse
    {
        $success = route('selfreflection.login.callback.success');
        $failure = route('selfreflection.login.callback.failure');

        $query = http_build_query([
            'app' => config('services.firefly.selfreflections.app'),
            'successURL' => $success,
            'failURL' => $failure,
        ]);

        return redirect($this->url.'/login/api/webgettoken?'.$query);
    }

    /**
     * @throws RequestException
     */
    public function callbackSuccess(Request $request): RedirectResponse
    {
        $request->validate([
            'ffauth_secret' => 'string|required',
        ]);

        $secret = $request->get('ffauth_secret');

        $this->getUserData($secret);

        return redirect()->route('selfreflection.home');
    }

    /**
     * @throws RequestException
     */
    public function getUserData(string $secret): Authenticatable
    {
        $response = Http::get($this->url.'/login/api/sso', [
            'ffauth_secret' => $secret,
            'ffauth_device_id' => config('services.firefly.selfreflections.app'),
        ]);

        $this->findOrCreateUserAndLogin($response->throw()->body());

        return auth()->user();
    }

    public function callbackFailure(): RedirectResponse
    {
        session()->flash('alert-danger', 'There was an issue with the Firefly authentication. Please try again.');

        return redirect()->route('selfreflection.login');
    }
}
