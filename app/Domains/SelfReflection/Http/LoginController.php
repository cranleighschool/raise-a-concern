<?php

namespace App\Domains\SelfReflection\Http;

use App\Http\Controllers\Auth\FireflyAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    use FireflyAuth;

    public string $url;

    protected string $redirectTo = '/';

    public function __construct()
    {
        $this->url = config('services.firefly.selfreflections.url');
    }

    public function redirectLogin(): RedirectResponse
    {
        $success = route('selfreflection.login.callback.success');
        $failure = route('selfreflection.login.callback.failure');

        $query = http_build_query([
            'app' => config('services.firefly.selfreflections.app'),
            'successURL' => $success,
            'failURL' => $failure,
        ]);

        return redirect($this->url . '/login/api/webgettoken?' . $query);
    }

    /**
     * @throws RequestException
     */
    public function callbackSuccess(Request $request): RedirectResponse
    {
        $request->validate([
            'ffauth_secret' => 'string|required',
        ]);

        $fireflyReponse = Http::get($this->url . '/login/api/sso', [
            'ffauth_secret' => $request->get('ffauth_secret'),
            'ffauth_device_id' => config('services.firefly.selfreflections.app'),
        ]);

        return $this->findOrCreateUserAndLogin($fireflyReponse, $request);
    }

    /**
     * @throws ValidationException
     */
    public function callbackFailure(): Response
    {
        session()->flash('alert-danger', 'There was an issue with the Firefly authentication. Please try again.');

        return $this->sendFailedLoginResponse(request());
    }
}
