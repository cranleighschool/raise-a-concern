<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.isams.batch_api_key', 'test-key');
    config()->set('services.isams.batch_api_url', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx');
});

it('shows the home page for guests with a login prompt', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $this->get(selfReflectionUrl('/'))
        ->assertSuccessful()
        ->assertSee('not logged in');
});

it('shows available report cycles to an authenticated pupil', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    $this->actingAs($pupil)
        ->get(selfReflectionUrl('/'))
        ->assertSuccessful()
        ->assertViewHas('reportCycles')
        ->assertSee('Active Cycle Report');
});

it('shows pupils-of-parent links to an authenticated parent', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
        'https://pastoral.cranleigh.org/*' => Http::response([
            ['pupil_id' => 1001, 'name' => 'Alice Smith', 'ncYear' => 11],
        ]),
    ]);

    $parent = User::factory()->asParent()->create();

    $this->actingAs($parent)
        ->get(selfReflectionUrl('/'))
        ->assertSuccessful()
        ->assertSee('Alice Smith');
});

it('shows a staff member a link to the pastoral module', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $staff = User::factory()->staff()->create();

    $this->actingAs($staff)
        ->get(selfReflectionUrl('/'))
        ->assertSuccessful()
        ->assertSee('Pastoral Module');
});
