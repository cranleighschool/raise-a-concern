<?php

use App\Domains\SelfReflection\Actions\ReportCycles;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.isams.batch_api_key', 'test-key');
    config()->set('services.isams.batch_api_url', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx');
});

// --- parent-can-view-pupil ---

it('denies non-parent users from viewing a pupil', function (string $state) {
    $user = User::factory()->{$state}()->create();
    $this->actingAs($user);

    $result = Gate::inspect('parent-can-view-pupil', 1001);

    expect($result->denied())->toBeTrue()
        ->and($result->message())->toBe('You are not a parent user');
})->with(['staff', 'pupil']);

it('allows a parent who is in the pupil contact list', function () {
    $parent = User::factory()->asParent()->create(['email' => 'parent@example.com']);
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['parent@example.com', 'other@example.com']),
    ]);

    $this->actingAs($parent);
    $result = Gate::inspect('parent-can-view-pupil', 1001);

    expect($result->allowed())->toBeTrue();
});

it('denies a parent who is not in the pupil contact list', function () {
    $parent = User::factory()->asParent()->create(['email' => 'outsider@example.com']);
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['parent@example.com']),
    ]);

    $this->actingAs($parent);
    $result = Gate::inspect('parent-can-view-pupil', 1001);

    expect($result->denied())->toBeTrue();
});

it('denies the parent gate when the pastoral module API returns an error', function () {
    $parent = User::factory()->asParent()->create(['email' => 'parent@example.com']);
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['message' => 'Pupil not found'], 404),
    ]);

    $this->actingAs($parent);
    $result = Gate::inspect('parent-can-view-pupil', 1001);

    expect($result->denied())->toBeTrue();
});

// --- report-editable ---

it('allows a pupil to edit a reflection before the cycle end date', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $pupil = User::factory()->pupil()->create();
    $this->actingAs($pupil);

    $cycle = ReportCycles::find(1);
    $result = Gate::inspect('report-editable', $cycle);

    expect($result->allowed())->toBeTrue();
});

it('denies editing after the cycle end date has passed', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml([
            ['id' => 1, 'name' => 'Expired', 'start' => '2020-01-01', 'end' => '2020-06-30', 'year' => 2020],
            ['id' => 2, 'name' => 'Also Expired', 'start' => '2020-01-01', 'end' => '2020-07-30', 'year' => 2020],
        ])),
    ]);

    $pupil = User::factory()->pupil()->create();
    $this->actingAs($pupil);

    $cycle = ReportCycles::find(1);
    $result = Gate::inspect('report-editable', $cycle);

    expect($result->allowed())->toBeFalse();
});

it('denies staff users from editing a reflection', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $staff = User::factory()->staff()->create();
    $this->actingAs($staff);

    $cycle = ReportCycles::find(1);
    $result = Gate::inspect('report-editable', $cycle);

    expect($result->allowed())->toBeFalse();
});

it('denies parent users from editing a reflection', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $parent = User::factory()->asParent()->create();
    $this->actingAs($parent);

    $cycle = ReportCycles::find(1);
    $result = Gate::inspect('report-editable', $cycle);

    expect($result->allowed())->toBeFalse();
});
