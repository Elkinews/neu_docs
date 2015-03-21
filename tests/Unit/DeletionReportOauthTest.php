<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletionReportOauthTest extends TestCase
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

    public function test_deletion_report_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));

        $data = [
            "profile" => 1,
            "orderBy" => "username asc",
            "offset" => 0,
            "limit" => 10,
            "from_date" => "01/14/2020",
            "to_date" => "01/12/2021",
            "user" => 375
        ];

        $response = $this->dashboardService->deletionReportOauth($data);

        $this->assertEquals($response['status'], 'success');
    }
}
