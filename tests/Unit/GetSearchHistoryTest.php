<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetSearchHistoryTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }



    /**
     * A GetSearchHistoryTest unit test example.
     *
     * @return void
     */
    public function test_get_search_history()
    {
        $data = ['profileId' => 1];
        $response = $this->dashboardService->getSearchHistory($data);
        $this->assertEquals($response['status'], 'success');
    }
}
