<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class CheckFieldHintOauthTest extends TestCase
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
     * A CheckFieldHintOauth unit test example.
     *
     * @return void
     */
    public function test_check_field_hint()
    {
        $data = [];
        $data['target_profile'] = 1;
        $data['source_table'] = "image";
        $data['prefix'] = "522";
        $data['field_name'] = "file_number";


        $fieldsParam = ['city' => 'Austin'];
        $data['fields'] = $fieldsParam;
        $response = $this->dashboardService->getCheckFieldHint($data);
        $this->assertEquals($response['status'], 'success');
    }
}
