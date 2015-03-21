<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetProfilesTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_get_profiles()
    {
        $response = $this->dashboardService->getProfiles();
        $this->assertEquals($response['status'], 'success');
        $body = $response['body'];
        $this->assertObjectHasAttribute('profiles', $response['body']->data);
    }
}
