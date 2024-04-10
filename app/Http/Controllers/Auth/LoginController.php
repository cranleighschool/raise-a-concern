<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use UnhandledMatchError;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use FireflyAuth;

    /**
     * Where to redirect users after login.
     */
    protected string $redirectTo = '/submit'; //RouteServiceProvider::HOME;

    public function showLoginForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('raiseaconcern.submit');
        }
        if (request()->host() === config('app.domains.selfreflection.url')) {
            return view('selfreflection.home');
        }

        return view('auth.login');
    }

    /**
     * @throws RequestException
     * @throws Exception
     */
    public function callbackSuccess(Request $request, string $school): RedirectResponse
    {
        if ($request->has('ffauth_secret')) {

            $subdomain = match ($school) {
                'senior' => 'cranleigh',
                'prep' => 'cranprep'
            };
            $output = Http::get('https://'.$subdomain.'.fireflycloud.net/login/api/sso', [
                'ffauth_device_id' => 'raiseaconcern-cranleigh',
                'ffauth_secret' => $request->get('ffauth_secret'),
            ])->throw()->body();

            $this->findOrCreateUserAndLogin($output);

            return redirect()->intended(route('raiseaconcern.submit'));
        }

        $debugarray = [
            'school' => $school,
            'request' => $request,
        ];
        if (isset($user)) {
            $debugarray['user'] = $user;
        }
        throw new Exception('Firefly Authentication Not Found', 400, $debugarray);
    }

    public function loginRedirect(string $school): RedirectResponse
    {
        try {
            $subdomain = match ($school) {
                'senior' => 'cranleigh',
                'prep' => 'cranprep'
            };
        } catch (UnhandledMatchError $exception) {
            Log::debug($exception->getMessage(), [
                'school' => $school,
                'request' => request(),
                'trace' => $exception->getTrace(),
            ]);
            abort(404, 'School not found.');
        }

        $url = route('raiseaconcern.firefly-success', $school);

        return redirect('https://'.$subdomain.'.fireflycloud.net/login/api/webgettoken?app=raiseaconcern-cranleigh&successURL='.$url);
    }
}
