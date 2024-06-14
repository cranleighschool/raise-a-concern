<?php

namespace App\Domains\SelfReflection\Actions;

use App\Exceptions\IsamsConnectionFailure;
use App\Exceptions\IsamsRequestException;
use App\Exceptions\ReportCycleNotFound;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

final class ReportCycles
{
    /**
     * @throws RequestException
     * @throws Exception
     */
    public static function all(bool $withoutFilter = false): Collection
    {
        $configKey = 'services.isams.batch_api_key';
        if (! config()->has($configKey) || is_null(config($configKey))) {
            throw new Exception('Missing configuration key: '.$configKey);
        }

        return (new self(withoutFilter: $withoutFilter))();
    }

    /**
     * @throws RequestException
     * @throws ReportCycleNotFound
     */
    public static function find(int $reportCycleId): object
    {
        $reportCycle = self::all(withoutFilter: true)->firstWhere('reportCycleId', $reportCycleId);

        if (is_object($reportCycle)) {
            return $reportCycle;
        }
        throw new ReportCycleNotFound('Report cycle not found', 404);
    }

    public function __construct(private readonly bool $withoutFilter = false)
    {
    }

    /**
     * @throws RequestException
     * @throws Exception
     */
    public function __invoke(): Collection
    {
        return $this->getReportCycles();
    }

    private function getCurrentAcademicYear(): int
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // If the current month is less than September, then we are in the previous calendar year
        return $currentMonth < 9 ? $currentYear - 1 : $currentYear;
    }

    /**
     * @throws Exception
     */
    public function getReportCycles(): Collection
    {
        if ($this->withoutFilter) {
            $filters = $this->filters();
        } else {
            $filters = $this->filters(
                reportYear: $this->getCurrentAcademicYear()
            );
        }

        try {
            $query = Http::withQueryParameters([
                'apikey' => config('services.isams.batch_api_key'),
            ])->withHeaders([
                'Content-Type' => 'application/xml; charset=utf-8',
            ])->send('POST', config('services.isams.batch_api_url'), [
                'body' => $filters,
            ])->throw()->body();

        } catch (ConnectionException $exception) {
            throw new IsamsConnectionFailure('Failed to connect to iSAMS API');
        } catch (RequestException $exception) {
            throw new IsamsRequestException($exception->response->body());
        }
        $xml = simplexml_load_string($query);
        $json = json_encode($xml);
        $result = json_decode($json);

        $reportCycles = $result->SchoolReports->ReportCycles->ReportCycle;

        return collect($reportCycles)->filter(function (object $reportCycle) {
            if (app()->environment('local') && app()->hasDebugModeEnabled()) {
                return true;
            }
            if ($this->withoutFilter) {
                return true;
            }
            return $reportCycle->EndDate > now();
        })->map(function (object $reportCycle) {
            $reportCycle->reportCycleId = $reportCycle->{'@attributes'}->Id;

            return $reportCycle;
        });
    }

    private function filters(?int $reportYear = null, int $reportCycleType = 0, ?int $reportTerm = null): string
    {
        return '<?xml version="1.0" encoding="utf-8" ?>
<Filters>
<MethodsToRun>
        <Method>SchoolReports_GetReportCycles</Method>
    </MethodsToRun>
    <SchoolReports>
        <ReportCycles reportYear="'.$reportYear.'" reportCycleType="'.$reportCycleType.'" reportTerm="'.$reportTerm.'"></ReportCycles>
    </SchoolReports>
</Filters>';
    }
}
