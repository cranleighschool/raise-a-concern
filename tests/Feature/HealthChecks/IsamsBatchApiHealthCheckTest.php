<?php

use App\Http\HealthChecks\IsamsBatchApiHealthCheck;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.isams.batch_api_key', 'test-key');
    config()->set('services.isams.batch_api_url', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx');
});

it('returns ok when active report cycles exist', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $result = (new IsamsBatchApiHealthCheck)->run();

    expect($result->status->value)->toBe('ok');
});

it('returns a warning when there are no active cycles but historical ones exist', function () {
    // Both cycles are in the past for the active filter, but exist for withoutFilter
    $pastOnlyXml = isamsReportCyclesXml([
        ['id' => 1, 'name' => 'Past A', 'start' => '2020-01-01', 'end' => '2020-06-30', 'year' => 2020],
        ['id' => 2, 'name' => 'Past B', 'start' => '2020-01-01', 'end' => '2020-07-30', 'year' => 2020],
    ]);

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response($pastOnlyXml),
    ]);

    $result = (new IsamsBatchApiHealthCheck)->run();

    expect($result->status->value)->toBe('warning');
});

it('returns failed when the iSAMS API throws an exception', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response('Error', 500),
    ]);

    $result = (new IsamsBatchApiHealthCheck)->run();

    expect($result->status->value)->toBe('failed');
});
