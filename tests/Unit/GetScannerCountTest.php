<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetScannerCountTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_get_scanner_count()
    {
        $param = [
            'serialNumber' => 'A20121031185154'
        ];
        $response = $this->dashboardService->getScannerCount($param);
        $this->assertEquals($response['status'], 'success');
    }
}
