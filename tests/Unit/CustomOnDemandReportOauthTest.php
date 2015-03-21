<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomOnDemandReportOauthTest extends TestCase
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

    public function test_custom_on_demand_report_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));

        $data = [
            'profile' => 207,
            "orderBy" => "username asc",
            "offset"=> 0,
            "limit"=> 10,
            "in_username"=> 375
        ];

        $response = $this->dashboardService->customOnDemandReportOauth($data);
        $this->assertEquals($response['status'], 'success');
    }
}
