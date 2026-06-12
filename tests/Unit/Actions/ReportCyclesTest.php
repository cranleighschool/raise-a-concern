<?php

use App\Domains\SelfReflection\Actions\ReportCycles;
use App\Exceptions\IsamsConnectionFailure;
use App\Exceptions\IsamsRequestException;
use App\Exceptions\ReportCycleNotFound;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.isams.batch_api_key', 'test-key');
    config()->set('services.isams.batch_api_url', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx');
});

it('retrieves all report cycles from iSAMS with withoutFilter true', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $cycles = ReportCycles::all(withoutFilter: true);

    expect($cycles)->toHaveCount(2);
});

it('filters out past report cycles when withoutFilter is false', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $cycles = ReportCycles::all(withoutFilter: false);

    expect($cycles)->toHaveCount(1)
        ->and($cycles->first()->CycleName)->toBe('Active Cycle');
});

it('includes the academic year in the XML filter payload', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $this->travelTo(now()->setMonth(10));
    ReportCycles::all(withoutFilter: false);

    $expectedYear = now()->year;
    Http::assertSent(fn ($request) => str_contains($request->body(), "reportYear=\"{$expectedYear}\""));
});

it('uses the previous calendar year as the academic year before September', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $this->travelTo(now()->setMonth(8)->setDay(31));
    ReportCycles::all(withoutFilter: false);

    $expectedYear = now()->year - 1;
    Http::assertSent(fn ($request) => str_contains($request->body(), "reportYear=\"{$expectedYear}\""));
});

it('uses the current calendar year as the academic year from September onwards', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $this->travelTo(now()->setMonth(9)->setDay(1));
    ReportCycles::all(withoutFilter: false);

    $expectedYear = now()->year;
    Http::assertSent(fn ($request) => str_contains($request->body(), "reportYear=\"{$expectedYear}\""));
});

it('throws IsamsConnectionFailure when the connection to iSAMS fails', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => function () {
            throw new ConnectionException('Connection refused');
        },
    ]);

    ReportCycles::all();
})->throws(IsamsConnectionFailure::class, 'Failed to connect to iSAMS API');

it('throws IsamsRequestException when iSAMS returns an error response', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response('Internal Server Error', 500),
    ]);

    ReportCycles::all();
})->throws(IsamsRequestException::class);

it('throws an Exception when the iSAMS API key is not configured', function () {
    config()->set('services.isams.batch_api_key', null);

    ReportCycles::all();
})->throws(Exception::class, 'Missing configuration key');

it('finds a specific report cycle by ID', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    $cycle = ReportCycles::find(1);

    expect($cycle->CycleName)->toBe('Active Cycle')
        ->and((int) $cycle->reportCycleId)->toBe(1);
});

it('throws ReportCycleNotFound when the ID does not exist', function () {
    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response(isamsReportCyclesXml()),
    ]);

    ReportCycles::find(999);
})->throws(ReportCycleNotFound::class, 'Report cycle not found');
