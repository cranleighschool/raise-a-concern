<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.isams.batch_api_key', 'test-key');
    config()->set('services.isams.batch_api_url', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx');
});

it('returns report cycles as JSON', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $this->getJson(selfReflectionUrl('/api/report-cycles'))
        ->assertSuccessful()
        ->assertJsonStructure([
            '*' => ['reportCycleId', 'CycleName', 'StartDate', 'EndDate', 'ReportYear'],
        ]);
});

it('returns all cycles including past ones when withoutFilter=1', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $response = $this->getJson(selfReflectionUrl('/api/report-cycles?withoutFilter=1'))
        ->assertSuccessful();

    expect($response->json())->toHaveCount(2);
});

it('returns only active cycles when withoutFilter=0', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $response = $this->getJson(selfReflectionUrl('/api/report-cycles?withoutFilter=0'))
        ->assertSuccessful();

    expect($response->json())->toHaveCount(1);
});

it('returns a 400 validation error when withoutFilter is not a boolean value', function () {
    $this->getJson(selfReflectionUrl('/api/report-cycles?withoutFilter=notabool'))
        ->assertStatus(400)
        ->assertJsonPath('error', 'There was an error with your request');
});

it('casts reportCycleId and ReportYear to integers in the response', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $cycle = $this->getJson(selfReflectionUrl('/api/report-cycles?withoutFilter=1'))
        ->assertSuccessful()
        ->json(0);

    expect($cycle['reportCycleId'])->toBeInt()
        ->and($cycle['ReportYear'])->toBeInt();
});

it('sorts the cycles by end date descending', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $cycles = $this->getJson(selfReflectionUrl('/api/report-cycles?withoutFilter=1'))
        ->assertSuccessful()
        ->json();

    expect($cycles[0]['EndDate'])->toBeGreaterThan($cycles[1]['EndDate']);
});

it('clears the cache and re-fetches when the refresh parameter is present', function () {
    Cache::put('get-all-report-cycles1', 'stale-data', now()->addDay());

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $this->getJson(selfReflectionUrl('/api/report-cycles?withoutFilter=1&refresh=1'))
        ->assertSuccessful();

    Http::assertSentCount(1);
});

it('uses the cached result on a second request without refresh', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $this->getJson(selfReflectionUrl('/api/report-cycles?withoutFilter=1'));
    $this->getJson(selfReflectionUrl('/api/report-cycles?withoutFilter=1'));

    Http::assertSentCount(1);
});
