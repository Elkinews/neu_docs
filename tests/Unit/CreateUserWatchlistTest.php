<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class CreateUserWatchlistTest extends TestCase
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
     * A CreateUserWatchlistTest unit test example.
     *
     * @return void
     */
    public function test_create_user_watchlist()
    {
        $fieldsParam = ['file_number' => '123'];
        $data = ['profile_id' => 1, 'fields' => $fieldsParam, 'action' => 'DOC_UPLOAD'];
        $response = $this->dashboardService->createUserWatchlist($data);
        $this->assertEquals($response['status'], 'success');
    }
}
