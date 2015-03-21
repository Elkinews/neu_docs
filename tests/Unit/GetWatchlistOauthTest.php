<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetWatchlistOauthTest extends TestCase
{


    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
    }

    /**
     * A GetWatchlistOauth unit test example.
     *
     * @return void
     */
    public function test_get_watchlist_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));
        $param = [
            'profileId' => 1,
            'orderBy' => "created_on",
            'orderDir' => "asc"
        ];
        $response = $this->dashboardService->getWatchlist($param);
        $this->assertEquals($response['status'], 'success');

    }
}
