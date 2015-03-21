<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class CreateDocumentTest extends TestCase
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
     * A EditDocIndexingFieldsTest unit test example.
     *
     * @return void
     */
    public function test_create_document()
    {
        $fieldsParam = ['file_number' => '99909', 'city' => 'cake town'];
        $data = ['profile_id' => 1, 'fields' => $fieldsParam];

        $response = $this->dashboardService->createDocument($data);

        $this->assertEquals($response['status'], 'success');
    }
}
