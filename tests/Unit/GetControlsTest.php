<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetControlsTest extends TestCase
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
     * A GetDoubleEntryTest unit test example.
     *
     * @return void
     */
    public function test_get_controls()
    {
        $params = [
            'profile' => 1,
            'targetProfileID' => 1
        ];


        $response = $this->dashboardService->getControls($params);

        $this->assertEquals($response['status'], 'success');
    }
}
