<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetDoubleEntryTest extends TestCase
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
    public function test_get_double_entry()
    {
        $params = [
            'fields' => '{"file_number":"8675309"}',
            'targetProfile' => 1
        ];


        $response = $this->dashboardService->getDoubleEntry($params);

        $this->assertEquals($response['status'], 'success');
    }
}
