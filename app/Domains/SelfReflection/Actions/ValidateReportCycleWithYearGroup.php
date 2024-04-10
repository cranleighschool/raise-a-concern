<?php

namespace App\Domains\SelfReflection\Actions;

use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * There's a chance this validation method is not accurate, as at the time of checking reports are likely
 * not populated with any data, and thus the "Reports" node will always be empty even if the report cycle
 * is valid. This is a placeholder implementation and should be reviewed.
 */
readonly class ValidateReportCycleWithYearGroup
{
    public function __construct(private int $reportCycleId, private int $ncYear)
    {
    }

    /**
     * @throws RequestException
     */
    public function __invoke(): bool
    {
        return $this->validate();
    }

    /**
     * @throws RequestException
     * @throws Exception
     */
    private function validate(): bool
    {
        $query = Http::withQueryParameters([
            'apikey' => config('services.isams.batch_api_key'),
        ])->withHeaders([
            'Content-Type' => 'application/xml; charset=utf-8',
        ])->send('POST', config('services.isams.batch_api_url'), [
            'body' => $this->filters(
                ncYear: $this->ncYear,
                reportCycleId: $this->reportCycleId
            ),
        ])->throw()->body();

        $xml = simplexml_load_string($query);
        $json = json_encode($xml);
        $result = json_decode($json);

        if (isset($result->SchoolReports->ReportCycles->ReportCycle->Reports)) {
            return true;
        } else {
            return false;
        }
    }

    private function filters(int $ncYear, int $reportCycleId): string
    {
        return '<?xml version="1.0" encoding="utf-8" ?>
                <Filters>
                    <MethodsToRun>
                        <Method>SchoolReports_GetReports</Method>
                    </MethodsToRun>
                    <SchoolReports>
                        <Reports ncYear="'.$ncYear.'" reportCycleIdsToInclude="'.$reportCycleId.'"></Reports>
                    </SchoolReports>
                </Filters>';
    }
}
