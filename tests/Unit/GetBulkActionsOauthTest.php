<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetBulkActionsOauthTest extends TestCase
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
     * A GetBulkActionsOauthTest unit test .
     *
     * @return void
     */
    public function test_get_bulk_actions_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));
        $param = [
            'profileId' => 1
        ];
        $response = $this->dashboardService->getBulkActions($param);
        $this->assertEquals($response['status'], 'success');
    }
}
