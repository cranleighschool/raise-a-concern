<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
});

it('shows the concern submission form to a guest', function () {
    $this->get('/submit')
        ->assertSuccessful()
        ->assertViewIs('home');
});

it('shows the concern form to an authenticated user', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['user_id' => 456]),
    ]);

    $this->actingAs(User::factory()->staff()->create())
        ->get('/submit')
        ->assertSuccessful()
        ->assertViewIs('home');
});

it('validates that person_type, subject and concern are all required', function () {
    $this->post('/submit', [])
        ->assertSessionHasErrors(['person_type', 'subject', 'concern']);
});

it('validates the subject minimum length of 2 characters', function () {
    $this->post('/submit', [
        'person_type' => 'pupil',
        'subject' => 'A',
        'concern' => 'A valid concern text.',
    ])->assertSessionHasErrors('subject');
});

it('validates the concern minimum length of 5 characters', function () {
    $this->post('/submit', [
        'person_type' => 'pupil',
        'subject' => 'Valid Subject',
        'concern' => 'Hi',
    ])->assertSessionHasErrors('concern');
});

it('validates the concern maximum length of 4096 characters', function () {
    $this->post('/submit', [
        'person_type' => 'pupil',
        'subject' => 'Valid Subject',
        'concern' => str_repeat('a', 4097),
    ])->assertSessionHasErrors('concern');
});

it('rejects an invalid school_id value', function () {
    $this->post('/submit', [
        'person_type' => 'pupil',
        'subject' => 'Valid Subject',
        'concern' => 'This is a valid concern.',
        'school_id' => 99,
    ])->assertSessionHasErrors('school_id');
});

it('submits a concern as a guest and shows the thank-you page', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['concern_id' => 123]),
    ]);

    $this->post('/submit', [
        'person_type' => 'pupil',
        'subject' => 'Test Subject',
        'concern' => 'This is a valid concern that needs addressing.',
        'school_id' => 1,
    ])->assertSuccessful()->assertViewIs('thankyou');

    $this->assertDatabaseHas('concerns_ip_address', ['concern_id' => 123]);
});

it('stores the concern as an authenticated user with a pastoral module user ID', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/api/v2/auth/users/make' => Http::response(['user_id' => 456]),
        'https://pastoral.cranleigh.org/api/v2/concerns/store' => Http::response(['concern_id' => 789]),
    ]);

    $user = User::factory()->staff()->create(['email' => 'staff@cranleigh.org']);

    $this->actingAs($user)
        ->post('/submit', [
            'person_type' => 'staff',
            'subject' => 'A Staff Concern',
            'concern' => 'This is a valid staff concern that needs addressing.',
            'school_id' => 1,
        ])->assertSuccessful()->assertViewIs('thankyou');
});

it('redirects back with an error message when the pastoral module API fails', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['error' => 'Server Error'], 500),
    ]);

    $this->post('/submit', [
        'person_type' => 'pupil',
        'subject' => 'Test Subject',
        'concern' => 'This is a valid concern that needs addressing.',
        'school_id' => 1,
    ])->assertRedirect()
        ->assertSessionHas('alert-danger');
});

it('shows the safeguarding team as reviewer for a pupil concern at senior school', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['concern_id' => 1]),
    ]);

    $response = $this->post('/submit', [
        'person_type' => 'pupil',
        'subject' => 'Subject',
        'concern' => 'A valid pupil concern.',
        'school_id' => 1,
    ]);

    $response->assertViewHas('reviewer', fn ($r) => str_contains($r, 'Cranleigh School'));
});

it('shows the safeguarding team as reviewer for a pupil concern at prep school', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['concern_id' => 1]),
    ]);

    $response = $this->post('/submit', [
        'person_type' => 'pupil',
        'subject' => 'Subject',
        'concern' => 'A valid pupil concern.',
        'school_id' => 2,
    ]);

    $response->assertViewHas('reviewer', fn ($r) => str_contains($r, 'Cranleigh Prep School'));
});

it('shows the Chair of Governors as reviewer for a concern about the head', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['concern_id' => 1]),
    ]);

    $response = $this->post('/submit', [
        'person_type' => 'head',
        'subject' => 'Subject',
        'concern' => 'A valid concern about the head.',
        'school_id' => 1,
    ]);

    $response->assertViewHas('reviewer', fn ($r) => str_contains($r, 'Chair of Governors'));
});

it('shows the head as reviewer for a staff concern at senior school', function () {
    Http::fake([
        'https://pastoral.cranleigh.org/*' => Http::response(['concern_id' => 1]),
    ]);

    $response = $this->post('/submit', [
        'person_type' => 'staff',
        'subject' => 'Subject',
        'concern' => 'A valid staff concern.',
        'school_id' => 1,
    ]);

    $response->assertViewHas('reviewer', fn ($r) => str_contains($r, 'the Head'));
});
