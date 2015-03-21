<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScannerUsageReportOauthTest extends TestCase
{

    use RefreshDatabase;

    protected $dashboardService;
    protected $ndeService;

    public function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
    }

    /**
     * @return void
     */
    public function test_scanner_usage_report_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));

        $data = [
            'profile' => 1,
            "orderBy" => "scanner asc",
            "offset" => 0,
            "limit" => 10,
            "from_date" => "01/14/2020",
            "to_date" => "01/12/2021",
            "user" => 0
        ];

        $response = $this->dashboardService->scannerUsageReportOauth($data);

        $this->assertEquals($response['status'], 'success');
    }
}
