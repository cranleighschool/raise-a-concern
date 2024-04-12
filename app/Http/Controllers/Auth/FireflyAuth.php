<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait FireflyAuth
{
    use AuthenticatesUsers, AuthorizesRequests;

    public function __construct() {
        $this->middleware('guest')->except('logout');
    }
    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();

        if ($request->server('HTTP_HOST') === config('app.domains.selfreflection.url')) {
            return redirect()->route('selfreflection.home');
        }

        return redirect('/');
    }

    /**
     * @throws RequestException
     * @throws Exception
     */
    private function findOrCreateUserAndLogin(Response $fireflyReponse, Request $request): RedirectResponse
    {
        $xmlString = $fireflyReponse->throw()->body();

        $obj = $this->convertXmlToObject($xmlString);

        $user = $this->getUserObject($obj->user->{'@attributes'});

        // log them in
        $this->guard()->login($user);
        // Update db with login time
        auth()->user()->update(['updated_at' => now()]);
        // Fake request data (for the sendLoginResponse method work)
        $request->merge(['email' => $user->email, 'username' => $user->email, 'password' => 'cranleigh12']);
        session()->flash('alert-success', 'You have logged in as: ' . auth()->user()->name);

        return $this->sendLoginResponse($request);
    }

    private function convertXmlToObject(string $xmlString): object
    {
        /**
         * We know this is a bit messy - but can't figure out a cleaner way to do it at the moment. DOMDocument was
         * not working as expected. If you can improve this, please do!
         */
        $xml = simplexml_load_string($xmlString);
        $json = json_encode($xml);

        return json_decode($json);
    }

    /**
     * @throws Exception
     */
    private function getUserObject(object $userData): User
    {
        $existingUser = User::query()->where('email', '=', $userData->email)->first();
        if (is_null($existingUser)) {
            // create a new user
            $ssoData = $this->getIdentifierItems($userData->identifier);
            $existingUser = User::create($userData->email, $ssoData['table'], $userData->name, $userData->username, $ssoData['id']);
        }
        if ($existingUser instanceof User) {
            return $existingUser;
        }
        throw new Exception('User not found');
    }

    private function getIdentifierItems(string $identifier): array
    {
        $parts = explode(':', $identifier);
        $isamsId = end($parts);
        $db = str_replace('iSAMS', '', $parts[3]);

        return [
            'table' => $db,
            'id' => (int)$isamsId,
        ];
    }
}
