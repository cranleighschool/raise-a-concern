<?php

use App\Domains\SelfReflection\Actions\ValidateReportCycleWithYearGroup;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.isams.batch_api_key', 'test-key');
    config()->set('services.isams.batch_api_url', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx');
});

it('returns true when reports exist for the given report cycle and year group', function () {
    $xml = '<?xml version="1.0" encoding="utf-8"?><iSAMS><SchoolReports><ReportCycles><ReportCycle Id="1"><Reports><Report Id="10"/></Reports></ReportCycle></ReportCycles></SchoolReports></iSAMS>';

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response($xml),
    ]);

    $result = (new ValidateReportCycleWithYearGroup(1, 11))();

    expect($result)->toBeTrue();
});

it('returns false when no reports exist for the given report cycle and year group', function () {
    $xml = '<?xml version="1.0" encoding="utf-8"?><iSAMS><SchoolReports><ReportCycles><ReportCycle Id="1"></ReportCycle></ReportCycles></SchoolReports></iSAMS>';

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response($xml),
    ]);

    $result = (new ValidateReportCycleWithYearGroup(1, 11))();

    expect($result)->toBeFalse();
});

it('includes the nc year and report cycle ID in the XML filter sent to iSAMS', function () {
    $xml = '<?xml version="1.0" encoding="utf-8"?><iSAMS><SchoolReports><ReportCycles><ReportCycle Id="5"></ReportCycle></ReportCycles></SchoolReports></iSAMS>';

    Http::fake([
        'https://isams.cranleigh.org/*' => Http::response($xml),
    ]);

    (new ValidateReportCycleWithYearGroup(5, 10))();

    Http::assertSent(function ($request) {
        return str_contains($request->body(), 'ncYear="10"')
            && str_contains($request->body(), 'reportCycleIdsToInclude="5"');
    });
});
