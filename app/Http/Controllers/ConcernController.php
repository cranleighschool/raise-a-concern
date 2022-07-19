<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ConcernController extends Controller
{
    /**
     * @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    private User|null $loggedInUser = null;
    /**
     * @var int|null
     */
    private int|null $pastoralModuleUserId = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->loggedInUser = auth()->user();
        $this->pastoralModuleUserId = $this->getPastoralModuleUserId();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $pastoralModuleUserId = $this->pastoralModuleUserId;

        return view('home', compact('pastoralModuleUserId'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function store(Request $request)
    {
        $request->validate([
            'person_type' => 'required|string',
            'subject' => 'required|string|min:2|max:50',
            'school' => [
                'nullable',
                Rule::in([1, 2], 'school_id'),
            ],
            'concern' => 'required|string|min:5',
        ]);
        $submitter = $this->getSubmitter();
        $data = $request->only(['person_type', 'school_id', 'subject', 'concern']);
        $data['submitter'] = $submitter;
        $data['api_token'] = config('pastoral-module.apiToken');

        try {
            $postData = Http::baseUrl(config('pastoral-module.apiUrl'))->acceptJson()->post('concerns/store', $data)->throw();
            if ($postData->ok()) {
                $concernId = $postData->object()->concern;
                try {
                    DB::table('concerns_ip_address')->insert([
                        'concern_id' => $concernId,
                        'ipaddress' => getRealIpAddress()
                    ]);
                } catch (QueryException $queryException) {
                    session()->flash("alert-warning", "Could not add IP Address to Database");
                }
                session()->flash("alert-success", "Submitted. Thank You.");
                return redirect()->route('home');
            }
        } catch (RequestException $exception) {
            session()->flash("alert-danger", "There was an error and your concern was not submitted. If this problem persists please email your concern to safeguarding@cranleigh.org");
            return redirect()->back()->withInput($request->only(['person_type', 'school_id', 'subject', 'concern']));
        }
    }



    /**
     * @return int|string
     */
    private function getSubmitter(): int|string
    {
        if ($this->loggedInUser && ! is_null($this->pastoralModuleUserId)) {
            return $this->pastoralModuleUserId;
        }
        if ($this->loggedInUser && is_null($this->pastoralModuleUserId)) {
            return auth()->user()->email;
        }
        return 'Unknown';
    }

    /**
     * @return int|null
     */
    private function getPastoralModuleUserId(): int|null
    {
        if ($this->loggedInUser) {
            try {
                $pmUser = Http::baseUrl(config('pastoral-module.apiUrl'))->acceptJson()->post('auth/users/make', [
                    "email" => $this->loggedInUser->email,
                    'api_token' => config('pastoral-module.apiToken'),
                ])->throw()->object();
            } catch (RequestException $exception) {
                Log::error($exception->getMessage());
                return null;
            }
            if (isset($pmUser->user_id)) {
                return $pmUser->user_id;
            }

        }

        return null;
    }
}
