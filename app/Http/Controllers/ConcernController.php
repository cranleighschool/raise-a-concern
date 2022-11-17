<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ConcernController extends Controller
{
    public const SENIOR_SCHOOL_ID = 1;
    public const PREP_SCHOOL_ID = 2;
    /**
     * @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    private User|null|\Illuminate\Contracts\Auth\Authenticatable $loggedInUser = null;
    /**
     * @var int|null
     */
    private int|null $pastoralModuleUserId = null;

    /**
     * @return void
     */
    private function setVars()
    {
        $this->loggedInUser = auth()->user();
        $this->pastoralModuleUserId = $this->getPastoralModuleUserId();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): \Illuminate\Contracts\Support\Renderable
    {
        $this->setVars();
        $pastoralModuleUserId = $this->pastoralModuleUserId;

        return view('home', compact('pastoralModuleUserId'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        $this->setVars();
        $request->validate([
            'person_type' => 'required|string',
            'subject' => 'required|string|min:2|max:50',
            'school_id' => 'nullable|in:1,2',
            'concern' => 'required|string|min:5|max:4096',
        ]);
        $data = $request->only(['person_type', 'school_id', 'subject', 'concern']);
        $data[ 'submitter' ] = $this->getSubmitter();

        $person = $data['person_type'];
        $school = $data['school_id'];
        try {
            $response = Http::withUserAgent("RaiseAConcern")
                            ->withToken(config('pastoral-module.apiToken'))
                            ->baseUrl(config('pastoral-module.apiUrl'))
                            ->acceptJson()
                            ->post('concerns/store', $data)
                            ->throw();

            $concernId = $response->object()->concern_id;
            $this->addIpAddressToDatabase($concernId);
            session()->flash("alert-success", "Submitted. Thank You.");
            $reviewer = $this->calculateRecipient($person, $school);
            return view('thankyou', ['concernId' => $concernId, 'reviewer' => $reviewer]);

        } catch (RequestException $exception) {
            Log::error($exception->getMessage());
            session()->flash("alert-danger",
                "There was an error and your concern was not submitted. If this problem persists please email your concern to safeguarding@cranleigh.org");
            return redirect()->back()->withInput($request->only(['person_type', 'school_id', 'subject', 'concern']));
        }
    }

    /**
     * @param  string  $person
     * @param  int|null  $school
     *
     * @return string
     */
    private function calculateRecipient(string $person, ?int $school): string
    {
        if ($person==='headmaster') {
            return "The Chair of Governors, ".config('people.CHAIR_OF_GOVERNORS').'.';
        }

        if ($person==='pupil') {
            $return = 'The safeguarding team';
            if ($school===self::SENIOR_SCHOOL_ID) {
                return $return.' at Cranleigh School.';
            }
            if ($school===self::PREP_SCHOOL_ID) {
                return $return.' at Cranleigh Prep School.';
            }
            return $return.'.';
        }
        if ($person==='staff') {
            $return = 'The Headmaster';
            if ($school===self::SENIOR_SCHOOL_ID) {
                return $return.', '.config('people.CS_HEAD').'.';
            }
            if ($school===self::PREP_SCHOOL_ID) {
                return $return.', '.config('people.CPS_HEAD').'.';
            }
            return $return.'.';
        }

        return 'the relevant safeguarding team member.';
    }

    /**
     * @param  int  $concernId
     *
     * @return void
     */
    private function addIpAddressToDatabase(int $concernId): void
    {
        try {
            DB::table('concerns_ip_address')->insert([
                'concern_id' => $concernId,
                'ipaddress' => getRealIpAddress(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $queryException) {
            Log::debug($queryException->getMessage());
            Log::error("Could not ad IP Address to Database", [
                'concern_id' => $concernId,
                'ip' => getRealIpAddress(),
            ]);
            session()->flash("alert-warning", "Could not add IP Address to Database");
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
            $pmUser = Cache::remember('pmUserFor'.$this->loggedInUser->email, now()->addMinutes(5), function () {
                try {
                    return Http::withUserAgent('RaiseAConcern')
                               ->baseUrl(config('pastoral-module.apiUrl'))
                               ->acceptJson()
                               ->post('auth/users/make', [
                                   "email" => $this->loggedInUser->email,
                                   'api_token' => config('pastoral-module.apiToken'),
                               ])->throw()->object();
                } catch (RequestException $exception) {
                    Log::error($exception->getMessage());
                    return null;
                }
            });

            if (isset($pmUser->user_id)) {
                return $pmUser->user_id;
            }

        }

        return null;
    }
}
