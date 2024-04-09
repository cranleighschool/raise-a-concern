<?php

namespace App\Domains\SelfReflection\Http;

use App\Domains\SelfReflection\Actions\ReportCycles;
use App\Http\Controllers\Auth\FireflyAuth;
use App\Models\User;
use DOMDocument;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
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

        return redirect($this->url . '/login/api/webgettoken?' . $query);
    }

    public function callbackSuccess(Request $request): View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $request->validate([
            'ffauth_secret' => 'string|required',
        ]);

        $secret = $request->get('ffauth_secret');

        $this->getUserData($secret);

        return view('selfreflection.home', [
            'reportCycles' => ReportCycles::all(),
        ]);
        return redirect(route('selfreflection.home'));
    }

    public function getUserData(string $secret): Authenticatable
    {
        $response = Http::get($this->url . '/login/api/sso', [
            'ffauth_secret' => $secret,
            'ffauth_device_id' => config('services.firefly.selfreflections.app')
        ]);

        $this->findOrCreateUserAndLogin($response->throw()->body());

        return auth()->user();
    }

    public function callbackFailure()
    {
        session()->flash('alert-danger', 'There was an issue with the Firefly authentication. Please try again.');
        return redirect(route('selfreflection.login'));
    }
}
