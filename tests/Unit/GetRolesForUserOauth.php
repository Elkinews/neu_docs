<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetRolesForUserOauth extends TestCase
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
     * A GetRolesForUserOauth unit test example.
     *
     * @return void
     */
    public function test_get_roles_for_user()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));
        $response = $this->dashboardService->getRolesForUser();
        $this->assertEquals($response['status'], 'success');
    }
}
