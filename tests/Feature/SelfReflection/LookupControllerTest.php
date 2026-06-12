<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.isams.batch_api_key', 'test-key');
    config()->set('services.isams.batch_api_url', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx');
});

it('redirects guests to the login page', function () {
    $this->get(selfReflectionUrl('/lookup/1'))
        ->assertRedirect();
});

it('returns 403 for staff users', function () {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff)
        ->get(selfReflectionUrl('/lookup/1'))
        ->assertForbidden();
});

it('redirects a pupil to their show page for the given report cycle', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData()),
    ]);

    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    $response = $this->actingAs($pupil)
        ->get(selfReflectionUrl('/lookup/1'));

    $response->assertRedirectToRoute('selfreflection.showget', [
        'reportCycle' => 1,
        'pupilId' => 1001,
    ]);
});

it('redirects a parent to the pupil show page for the given report cycle', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/api/v2/selfreflections/pupils/1001/contacts' => Http::response(['parent@example.com']),
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData()),
    ]);

    $parent = User::factory()->asParent()->create(['email' => 'parent@example.com']);

    $response = $this->actingAs($parent)
        ->get(selfReflectionUrl('/lookup/1'));

    $response->assertRedirectToRoute('selfreflection.showget', [
        'reportCycle' => 1,
        'pupilId' => 1001,
    ]);
});
