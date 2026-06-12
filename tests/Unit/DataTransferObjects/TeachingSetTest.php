<?php

use App\Domains\SelfReflection\DataTransferObjects\Teacher;
use App\Domains\SelfReflection\DataTransferObjects\TeachingSet;

function makeTeachingSetData(array $overrides = []): stdClass
{
    return (object) array_merge([
        'id' => 501,
        'subject' => 'Mathematics',
        'teachers' => [
            (object) [
                'staff_id' => 201,
                'name' => 'Mr Jones',
                'title' => 'Mr',
                'surname' => 'Jones',
                'username' => 'rjones',
            ],
        ],
    ], $overrides);
}

it('creates a TeachingSet from valid data', function () {
    $set = new TeachingSet(makeTeachingSetData());

    expect($set->id)->toBe(501)
        ->and($set->subject)->toBe('Mathematics')
        ->and($set->teachers)->toHaveCount(1)
        ->and($set->teachers->first())->toBeInstanceOf(Teacher::class);
});

it('casts multiple teachers to Teacher instances', function () {
    $data = makeTeachingSetData([
        'teachers' => [
            (object) ['staff_id' => 201, 'name' => 'Mr Jones', 'title' => 'Mr', 'surname' => 'Jones', 'username' => 'rjones'],
            (object) ['staff_id' => 202, 'name' => 'Mrs Brown', 'title' => 'Mrs', 'surname' => 'Brown', 'username' => 'sbrown'],
        ],
    ]);
    $set = new TeachingSet($data);

    expect($set->teachers)->toHaveCount(2)
        ->and($set->teachers->every(fn ($t) => $t instanceof Teacher))->toBeTrue();
});

it('throws when id is missing', function () {
    new TeachingSet((object) [
        'subject' => 'Maths',
        'teachers' => [(object) ['staff_id' => 1, 'name' => 'A', 'title' => 'Mr', 'surname' => 'A', 'username' => 'a']],
    ]);
})->throws(Exception::class, 'TeachingSet ID is required');

it('throws when subject is missing', function () {
    new TeachingSet((object) [
        'id' => 1,
        'teachers' => [(object) ['staff_id' => 1, 'name' => 'A', 'title' => 'Mr', 'surname' => 'A', 'username' => 'a']],
    ]);
})->throws(Exception::class, 'TeachingSet subject is required');

it('throws when teachers key is missing', function () {
    new TeachingSet((object) ['id' => 1, 'subject' => 'Maths']);
})->throws(Exception::class, 'TeachingSet teachers are required');

it('throws when teachers array is empty', function () {
    new TeachingSet((object) ['id' => 1, 'subject' => 'Maths', 'teachers' => []]);
})->throws(Exception::class, 'TeachingSet must have at least one teacher');
