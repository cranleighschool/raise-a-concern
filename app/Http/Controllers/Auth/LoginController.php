<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
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

    /**
     * @param string $identifier
     *
     * @return array
     */
    private function getIdentifierItems(string $identifier): array
    {
        $parts = explode(":", $identifier);
        $isamsId = end($parts);
        $db = str_replace("iSAMS", "", $parts[3]);

        return [
            'table' => $db,
            'id' => (int)$isamsId,
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $school
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function callbackSuccess(Request $request, string $school): RedirectResponse
    {
        if ($request->has('ffauth_secret')) {

            $subdomain = match ($school) {
                'senior' => 'cranleigh',
                'prep' => 'cranprep'
            };
            $output = Http::get('https://' . $subdomain . '.fireflycloud.net/login/api/sso', [
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
                // Update db with login time
                auth()->user()->update(['updated_at' => now()]);
            } else {
                // create a new user
                $ssoData = $this->getIdentifierItems($user->identifier);
                $user = User::create($user->email, $ssoData['table'], $user->name, $user->username, $ssoData['id']);
                auth()->login($user);
            }
            // Let them know they've logged in
            session()->flash("alert-success", "You have logged in as: " . auth()->user()->name . " (" . auth()->user()->sso_type . ")");

            return redirect()->to('/submit');
        }

        throw new \Exception("Firefly Authentication Not Found", 400);
    }


    /**
     * @param string $school
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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
                'trace' => $exception->getTrace()
            ]);
            abort(404, "School not found.");
        }

        $url = route('firefly-success', $school);

        return redirect('https://' . $subdomain . '.fireflycloud.net/login/api/webgettoken?app=raiseaconcern-cranleigh&successURL=' . $url);
    }
}
