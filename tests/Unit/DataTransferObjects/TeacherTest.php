<?php

use App\Domains\SelfReflection\DataTransferObjects\Teacher;

function makeTeacherData(array $overrides = []): stdClass
{
    return (object) array_merge([
        'staff_id' => 201,
        'name' => 'Mr Jones',
        'title' => 'Mr',
        'surname' => 'Jones',
        'username' => 'rjones',
    ], $overrides);
}

it('creates a Teacher from valid data', function () {
    $teacher = new Teacher(makeTeacherData());

    expect($teacher->staff_id)->toBe(201)
        ->and($teacher->name)->toBe('Mr Jones')
        ->and($teacher->title)->toBe('Mr')
        ->and($teacher->surname)->toBe('Jones')
        ->and($teacher->username)->toBe('rjones');
});

it('throws when staff_id is missing', function () {
    new Teacher((object) ['name' => 'Mr Jones', 'title' => 'Mr', 'surname' => 'Jones', 'username' => 'rjones']);
})->throws(Exception::class, 'Teacher ID is required');

it('throws when name is missing', function () {
    new Teacher((object) ['staff_id' => 201, 'title' => 'Mr', 'surname' => 'Jones', 'username' => 'rjones']);
})->throws(Exception::class, 'Teacher name is required');

it('throws when title is missing', function () {
    new Teacher((object) ['staff_id' => 201, 'name' => 'Mr Jones', 'surname' => 'Jones', 'username' => 'rjones']);
})->throws(Exception::class, 'Teacher title is required');

it('throws when surname is missing', function () {
    new Teacher((object) ['staff_id' => 201, 'name' => 'Mr Jones', 'title' => 'Mr', 'username' => 'rjones']);
})->throws(Exception::class, 'Teacher surname is required');

it('throws when username is missing', function () {
    new Teacher((object) ['staff_id' => 201, 'name' => 'Mr Jones', 'title' => 'Mr', 'surname' => 'Jones']);
})->throws(Exception::class, 'Teacher username is required');
