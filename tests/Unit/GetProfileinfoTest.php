<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetProfileinfoTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_get_profileinfo()
    {
        $param = [
            'profile' => 1
        ];
        $response = $this->dashboardService->getProfileinfo($param);
        $this->assertEquals($response['status'], 'success');
    }
}
