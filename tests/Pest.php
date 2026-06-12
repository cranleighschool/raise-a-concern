<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(
    TestCase::class,
    RefreshDatabase::class,
)->in('Feature');

uses(TestCase::class)->in('Unit');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

beforeEach(function () {
    Cache::put('githubReleaseVersion', 'v1.0.0 - 2025-01-01 00:00:00', now()->addWeek());
})->in('Feature');

/**
 * Returns a full URL for a self-reflection domain path.
 * Use this instead of withServerVariables — Symfony always parses HTTP_HOST from the URL.
 */
function selfReflectionUrl(string $path = '/'): string
{
    return 'https://'.config('app.domains.selfreflection.url').'/'.ltrim($path, '/');
}

/**
 * Generates iSAMS XML response for report cycles.
 *
 * @param  array<array{id: int, name: string, start: string, end: string, year: int}>|null  $cycles
 */
function isamsReportCyclesXml(?array $cycles = null): string
{
    if ($cycles === null) {
        $cycles = [
            ['id' => 1, 'name' => 'Active Cycle', 'start' => '2025-09-01', 'end' => '2099-06-30', 'year' => 2025],
            ['id' => 2, 'name' => 'Past Cycle', 'start' => '2020-09-01', 'end' => '2021-06-30', 'year' => 2020],
        ];
    }

    $cyclesXml = collect($cycles)->map(fn ($c) => sprintf(
        '<ReportCycle Id="%d"><CycleName>%s</CycleName><ReportName>%s Report</ReportName><StartDate>%s</StartDate><EndDate>%s</EndDate><ReportYear>%d</ReportYear><LastUpdated>%s</LastUpdated></ReportCycle>',
        $c['id'], $c['name'], $c['name'], $c['start'], $c['end'], $c['year'], $c['start']
    ))->implode('');

    return '<?xml version="1.0" encoding="utf-8"?><iSAMS><SchoolReports><ReportCycles>'.$cyclesXml.'</ReportCycles></SchoolReports></iSAMS>';
}

/**
 * Returns a typical Pastoral Module pupil data response body.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function pastoralPupilData(array $overrides = []): array
{
    return [
        'data' => array_merge([
            'pupil_id' => 1001,
            'isamsId' => 'ISM001',
            'username' => 'jsmith',
            'prename' => 'John',
            'surname' => 'Smith',
            'ncYear' => 11,
            'teachingSets' => [
                [
                    'id' => 501,
                    'subject' => 'Mathematics',
                    'teachers' => [[
                        'staff_id' => 201,
                        'name' => 'Mr Jones',
                        'title' => 'Mr',
                        'surname' => 'Jones',
                        'username' => 'rjones',
                    ]],
                ],
                [
                    'id' => 502,
                    'subject' => 'English',
                    'teachers' => [[
                        'staff_id' => 202,
                        'name' => 'Mrs Brown',
                        'title' => 'Mrs',
                        'surname' => 'Brown',
                        'username' => 'sbrown',
                    ]],
                ],
            ],
        ], $overrides),
    ];
}

/**
 * Returns a fake Firefly SSO XML response for authentication tests.
 */
function fireflyXmlResponse(
    string $email = 'test@cranleigh.org',
    string $name = 'Test User',
    string $username = 'TUSER',
    string $ssoType = 'stu',
    int $ssoId = 12345
): string {
    $identifier = "cranleigh:school:type:iSAMS{$ssoType}:{$ssoId}";

    return "<?xml version=\"1.0\" encoding=\"utf-8\"?><authentication><user email=\"{$email}\" name=\"{$name}\" username=\"{$username}\" identifier=\"{$identifier}\"/></authentication>";
}

function something()
{
    // ..
}
