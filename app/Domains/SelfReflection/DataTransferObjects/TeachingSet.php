<?php

namespace App\Domains\SelfReflection\DataTransferObjects;

use Exception;
use Illuminate\Support\Collection;
use stdClass;

class TeachingSet
{
    public int $id;
    public string $subject;
    public Collection $teachers;

    /**
     * @throws Exception
     */
    public function __construct(stdClass $data)
    {
        if (!isset($data->id)) {
            throw new Exception('TeachingSet ID is required');
        }
        if (!isset($data->subject)) {
            throw new Exception('TeachingSet subject is required');
        }
        if (!isset($data->teachers)) {
            throw new Exception('TeachingSet teachers are required');
        }

        $this->id = $data->id;
        $this->subject = $data->subject;
        $this->teachers = collect($data->teachers)
            ->mapInto(Teacher::class)
            ->ensure(Teacher::class);

        if ($this->teachers->isEmpty()) {
            throw new Exception('TeachingSet must have at least one teacher');
        }
    }

}
