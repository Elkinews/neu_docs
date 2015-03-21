<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPossibleSearchDataOauth extends TestCase
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
     * A GetPossibleSearchDataOauth unit test example.
     *
     * @return void
     */
    public function test_get_possible_search_data()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));
        $param = [
            'profileId' => 1,
            'searchKey' => "file_number",
            'searchValue' => "123"
        ];
        $response = $this->dashboardService->getPossibleSearchData($param);
        $this->assertEquals($response['status'], 'success');

    }
}
