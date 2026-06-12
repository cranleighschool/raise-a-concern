<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.isams.batch_api_key', 'test-key');
    config()->set('services.isams.batch_api_url', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx');
});

// --- chooseCycle ---

it('redirects guests away from chooseCycle', function () {
    $this->post(selfReflectionUrl('/cycle'), ['reportCycle' => 1])
        ->assertRedirect();
});

it('validates that reportCycle is required on chooseCycle', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData()),
    ]);

    $this->actingAs($pupil)
        ->post(selfReflectionUrl('/cycle'), [])
        ->assertSessionHasErrors('reportCycle');
});

it('redirects to the show page after choosing a cycle', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData()),
    ]);

    $this->actingAs($pupil)
        ->post(selfReflectionUrl('/cycle'), ['reportCycle' => 1])
        ->assertRedirectToRoute('selfreflection.showget', [
            'reportCycle' => 1,
            'pupilId' => 1001,
        ]);
});

// --- show ---

it('redirects guests away from the show page', function () {
    $this->get(selfReflectionUrl('/1/pupil/1001'))
        ->assertRedirect();
});

it('shows a pupil their own reflections', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
        'https://pastoral.cranleigh.org/api/v2/selfreflections/reports/*/pupil/*' => Http::response([
            ['teaching_set_id' => 501, 'teacher_id' => 201, 'reflection' => 'My first reflection.'],
        ]),
        'https://pastoral.cranleigh.org/api/v2/selfreflections/find-pupil' => Http::response(pastoralPupilData()),
    ]);

    $this->actingAs($pupil)
        ->get(selfReflectionUrl('/1/pupil/1001'))
        ->assertSuccessful()
        ->assertViewIs('selfreflection.dataentry');
});

it("prevents a pupil from viewing another pupil's reflections", function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData(['pupil_id' => 9999])),
    ]);

    $this->actingAs($pupil)
        ->get(selfReflectionUrl('/1/pupil/1001'))
        ->assertForbidden();
});

// --- index (parent view) ---

it('redirects guests away from the parent pupil index', function () {
    $this->get(selfReflectionUrl('/pupil/1001'))
        ->assertRedirect();
});

it("shows an authorised parent the pupil's report cycles", function () {
    $parent = User::factory()->asParent()->create(['email' => 'parent@example.com']);

    Http::fake([
        'https://pastoral.cranleigh.org/api/v2/selfreflections/pupils/1001/contacts' => Http::response(['parent@example.com']),
        'https://pastoral.cranleigh.org/api/v2/selfreflections/pupils/1001/reflections' => Http::response([]),
        'https://pastoral.cranleigh.org/api/v2/selfreflections/find-pupil' => Http::response(pastoralPupilData()),
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $this->actingAs($parent)
        ->get(selfReflectionUrl('/pupil/1001'))
        ->assertSuccessful()
        ->assertViewIs('selfreflection.index');
});

it('denies a parent who is not a contact for the pupil', function () {
    $parent = User::factory()->asParent()->create(['email' => 'other@example.com']);

    Http::fake([
        'https://pastoral.cranleigh.org/api/v2/selfreflections/pupils/1001/contacts' => Http::response(['parent@example.com']),
    ]);

    $this->actingAs($parent)
        ->get(selfReflectionUrl('/pupil/1001'))
        ->assertForbidden();
});

// --- edit ---

it('redirects guests away from the edit page', function () {
    $this->get(selfReflectionUrl('/1/compose/501/201'))
        ->assertRedirect();
});

it('returns 403 when the report cycle edit window has closed', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml([
            ['id' => 1, 'name' => 'Closed Cycle', 'start' => '2020-01-01', 'end' => '2020-06-30', 'year' => 2020],
            ['id' => 2, 'name' => 'Other Cycle', 'start' => '2020-01-01', 'end' => '2020-07-30', 'year' => 2020],
        ])),
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData()),
    ]);

    $this->actingAs($pupil)
        ->get(selfReflectionUrl('/1/compose/501/201'))
        ->assertForbidden();
});

it('returns 403 when the teaching set and teacher combination is invalid on edit', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData()),
    ]);

    $this->actingAs($pupil)
        ->get(selfReflectionUrl('/1/compose/999/201'))
        ->assertForbidden();
});

// --- store ---

it('redirects guests away from the store action', function () {
    $this->post(selfReflectionUrl('/1/save/501/201'), ['reflection' => 'Some text here.'])
        ->assertRedirect();
});

it('validates that the reflection is required', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData()),
    ]);

    $this->actingAs($pupil)
        ->post(selfReflectionUrl('/1/save/501/201'), ['reflection' => ''])
        ->assertSessionHasErrors('reflection');
});

it('validates that the reflection meets the minimum length of 10 characters', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData()),
    ]);

    $this->actingAs($pupil)
        ->post(selfReflectionUrl('/1/save/501/201'), ['reflection' => 'Short'])
        ->assertSessionHasErrors('reflection');
});

it('strips emoji from reflections and saves successfully', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
        'https://pastoral.cranleigh.org/api/v2/selfreflections/find-pupil' => Http::response(pastoralPupilData()),
        'https://pastoral.cranleigh.org/api/v2/selfreflections/reports/*' => Http::response(['success' => true]),
    ]);

    $this->actingAs($pupil)
        ->post(selfReflectionUrl('/1/save/501/201'), [
            'reflection' => 'My reflection with emoji in it here. 😀',
        ])
        ->assertRedirectToRoute('selfreflection.showget', [
            'reportCycle' => 1,
            'pupilId' => 1001,
        ])
        ->assertSessionHas('alert-success');
});

it('saves a valid reflection and redirects to the show page', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
        'https://pastoral.cranleigh.org/api/v2/selfreflections/find-pupil' => Http::response(pastoralPupilData()),
        'https://pastoral.cranleigh.org/api/v2/selfreflections/reports/*/pupil/*' => Http::response(['success' => true]),
    ]);

    $this->actingAs($pupil)
        ->post(selfReflectionUrl('/1/save/501/201'), [
            'reflection' => 'This is a meaningful reflection about my progress in mathematics this term.',
        ])
        ->assertRedirectToRoute('selfreflection.showget', [
            'reportCycle' => 1,
            'pupilId' => 1001,
        ])->assertSessionHas('alert-success');
});

it('returns 403 when the teaching set and teacher combination is invalid on store', function () {
    $pupil = User::factory()->pupil()->create(['username' => 'jsmith']);

    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(pastoralPupilData()),
    ]);

    $this->actingAs($pupil)
        ->post(selfReflectionUrl('/1/save/999/201'), [
            'reflection' => 'This is a meaningful reflection about my progress.',
        ])->assertForbidden();
});
