<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetEndpointsOauth extends TestCase
{
    use RefreshDatabase;

    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
    }


    /**
     * A GetEndpointsOauth unit test example.
     *
     * @return void
     */
    public function test_get_endpoints()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));
        $response = $this->dashboardService->getEndpoints();
        $this->assertEquals($response['status'], 'success');
    }
}
