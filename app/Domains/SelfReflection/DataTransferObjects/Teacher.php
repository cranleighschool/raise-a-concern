<?php

namespace App\Domains\SelfReflection\DataTransferObjects;

use Exception;

class Teacher
{
    public int $staff_id;

    public string $name;

    public string $title;

    public string $surname;

    public string $username;

    /**
     * @throws Exception
     */
    public function __construct(\stdClass $data)
    {
        if (! isset($data->staff_id)) {
            throw new Exception('Teacher ID is required');
        }
        if (! isset($data->name)) {
            throw new Exception('Teacher name is required');
        }
        if (! isset($data->title)) {
            throw new Exception('Teacher title is required');
        }
        if (! isset($data->surname)) {
            throw new Exception('Teacher surname is required');
        }
        if (! isset($data->username)) {
            throw new Exception('Teacher username is required');
        }

        $this->staff_id = $data->staff_id;
        $this->name = $data->name;
        $this->title = $data->title;
        $this->surname = $data->surname;
        $this->username = $data->username;
    }
}
