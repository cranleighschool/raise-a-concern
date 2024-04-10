<?php

namespace App\Domains\SelfReflection\Actions;

use App\Domains\SelfReflection\DataTransferObjects\TeachingSet;
use App\Exceptions\PastoralModuleConnectionFailure;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

readonly class PupilData
{
    public int $pupil_id;

    public string $isamsId;

    public string $username;

    public string $prename;

    public string $surname;

    public int $ncYear;

    public Collection $teachingSets;

    /**
     * @throws Exception
     */
    public function __construct(null|int|string $pupil = null)
    {
        if (is_null($pupil)) {
            $pupil = auth()->user()->username;
        }

        if (is_string($pupil)) {
            $pupil = strtolower($pupil);
        }

        $authUsername = strtolower(auth()->user()->username);

        if (auth()->user()->isPupil() && is_string($pupil) && $pupil !== $authUsername) {
            throw new Exception("You are not allowed to view this pupil's data", 403);
        }

        if (auth()->user()->isParent() && is_int($pupil)) {
            Gate::authorize('parent-can-view-pupil', $pupil);
        }

        try {
            $result = Http::pastoralModule()
                ->post('selfreflections/find-pupil', $this->getQuery($pupil))
                ->throw()
                ->object();
        } catch (ConnectionException $exception) {
            throw new PastoralModuleConnectionFailure('Failed to connect to ISAMS', 503);
        }

        if (is_int($pupil) && auth()->user()->isPupil() && strtolower($result->data->username) !== $authUsername) {
            throw new Exception("You are not allowed to view this pupil's data", 403);
        }

        foreach ($result->data as $key => $data) {
            if ($key === 'teachingSets') {
                $this->teachingSets = collect($data)
                    ->mapInto(TeachingSet::class)
                    ->ensure(TeachingSet::class);

                continue;
            }
            $this->{$key} = $data;
        }
    }

    /**
     * @throws Exception
     */
    private function getQuery($pupil): array
    {
        $query = null;
        if (is_int($pupil)) {
            $query = [
                'pupil_id' => $pupil,
            ];
        }
        if (is_string($pupil)) {
            $query = [
                'username' => $pupil,
            ];
        }
        if (is_null($query)) {
            throw new Exception('Invalid query');
        }

        return $query;
    }

    /**
     * @throws Exception
     */
    public static function get(string $value)
    {
        $api = new self();
        if (isset($api->$value)) {
            return $api->$value;
        }
        throw new Exception("Property $value does not exist on GetPupilData");
    }
}
