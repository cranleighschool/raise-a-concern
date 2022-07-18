<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('redirect', [\App\Http\Controllers\Auth\LoginController::class, 'googleRedirect']);
Route::get('callback', [\App\Http\Controllers\Auth\LoginController::class, 'googleRedirect']);

Route::any('fireflyCallback', function (\Illuminate\Http\Request $request) {
    if ($request->has('ffauth_secret')) {

        $output = \Illuminate\Support\Facades\Http::get('https://cranleigh.fireflycloud.net/login/api/sso', [
            'ffauth_device_id' => 'raiseaconcern-cranleigh',
            'ffauth_secret' => $request->get('ffauth_secret'),
        ])->throw()->body();

        $xml = simplexml_load_string($output);
        $json = json_encode($xml);
        $array = json_decode($json);

        $user = $array->user->{'@attributes'};
        $user = User::create($user->email, "firefly", $user->name, $user->username);

        dd($array->user->{'@attributes'});
    }
    \Illuminate\Support\Facades\Log::debug($request->all());
});
Route::get('firefly/{school}/success', function (\Illuminate\Http\Request $request, string $school) {
    if ($request->has('ffauth_secret')) {
        $host = match ($school) {
            'senior' => 'https://cranleigh.fireflycloud.net',
            'prep' => 'https://cranprep.fireflycloud.net'
        };
        $output = \Illuminate\Support\Facades\Http::get($host.'/login/api/sso', [
            'ffauth_device_id' => 'raiseaconcern-cranleigh',
            'ffauth_secret' => $request->get('ffauth_secret'),
        ])->throw()->body();

        $xml = simplexml_load_string($output);
        $json = json_encode($xml);
        $array = json_decode($json);

        $user = $array->user->{'@attributes'};

        $existingUser = User::where('email', $user->email)->first();
        if($existingUser){
            // log them in
            auth()->login($existingUser, true);
        } else {
            // create a new user
            $user = User::create($user->email, "firefly-".$school, $user->name, $user->username);
            auth()->login($user, true);
        }



        return redirect()->to('/home');
    }
});

Route::get('firefly/{school}', function (string $school) {
    $host = match ($school) {
        'senior' => 'https://cranleigh.fireflycloud.net',
        'prep' => 'https://cranprep.fireflycloud.net'
    };
    $url = url('firefly/'.$school.'/success');
    return redirect($host.'/login/api/webgettoken?app=raiseaconcern-cranleigh&successURL='.$url);
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
