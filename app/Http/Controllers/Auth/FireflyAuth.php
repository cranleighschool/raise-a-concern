<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait FireflyAuth
{
    use AuthenticatesUsers;

    private function getIdentifierItems(string $identifier): array
    {
        $parts = explode(':', $identifier);
        $isamsId = end($parts);
        $db = str_replace('iSAMS', '', $parts[3]);

        return [
            'table' => $db,
            'id' => (int) $isamsId,
        ];
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();

        if ($request->server('HTTP_HOST') === config('app.domains.selfreflection.url')) {
            return redirect()->route('selfreflection.home');
        }

        return redirect('/');
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

    private function findOrCreateUserAndLogin(string $xmlString): void
    {
        $obj = $this->convertXmlToObject($xmlString);

        $user = $obj->user->{'@attributes'};
        $existingUser = User::query()->where('email', $user->email)->first();

        if (is_null($existingUser)) {
            // create a new user
            $ssoData = $this->getIdentifierItems($user->identifier);
            $existingUser = User::create($user->email, $ssoData['table'], $user->name, $user->username, $ssoData['id']);
        }

        // log them in
        session()->flush();
        Auth::login($existingUser, true);
        // Update db with login time
        auth()->user()->update(['updated_at' => now()]);

        // Let them know they've logged in
        session()->flash('alert-success', 'You have logged in as: '.auth()->user()->name);
    }
}
