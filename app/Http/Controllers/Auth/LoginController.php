<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    private function getIdentifierItems(string $identifier): array
    {
        $parts = explode(":", $identifier);
        $isamsId = end($parts);
        $db = str_replace("iSAMS", "", $parts[ 3 ]);

        return [
            'table' => $db,
            'id' => (int) $isamsId,
        ];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $school
     *
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function callbackSuccess(Request $request, string $school)
    {
        if ($request->has('ffauth_secret')) {
            $host = match ($school) {
                'senior' => 'https://cranleigh.fireflycloud.net',
                'prep' => 'https://cranprep.fireflycloud.net'
            };
            $output = Http::get($host.'/login/api/sso', [
                'ffauth_device_id' => 'raiseaconcern-cranleigh',
                'ffauth_secret' => $request->get('ffauth_secret'),
            ])->throw()->body();

            $xml = simplexml_load_string($output);
            $json = json_encode($xml);
            $array = json_decode($json);

            $user = $array->user->{'@attributes'};

            $existingUser = User::where('email', $user->email)->first();
            if ($existingUser) {
                // log them in
                auth()->login($existingUser);
            } else {
                // create a new user
                $ssoData = $this->getIdentifierItems($user->identifier);
                $user = User::create($user->email, $ssoData['table'], $user->name, $user->username, $ssoData['id']);
                auth()->login($user);
            }

            return redirect()->to('/submit');
        }
    }

    /**
     * @param  string  $school
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loginRedirect(string $school)
    {
        $host = match ($school) {
            'senior' => 'https://cranleigh.fireflycloud.net',
            'prep' => 'https://cranprep.fireflycloud.net'
        };

        $url = route('firefly-success', $school);

        return redirect($host.'/login/api/webgettoken?app=raiseaconcern-cranleigh&successURL='.$url);
    }
}
