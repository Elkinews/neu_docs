<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class UpdateQueueStatusTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_update_queue_status()
    {
        $fieldsParam = [['field_name' => 'file_number', 'field_value' => '123']];
        $params = ['profile_id' => 6, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];
        $response = $this->dashboardService->searchImages($params);
        $id = $response['body']->data->search_results->images[0]->image_id;
        $params = [
            'profileId' => 6,
            'docIds' => [$id],
            'cmd' => 'ADV'
        ];
        $response = $this->dashboardService->updateQueueStatus($params);
        $this->assertEquals($response['status'], 'success');
        $params = [
            'profileId' => 6,
            'docIds' => [$id],
            'cmd' => 'UNDO'
        ];
        $response = $this->dashboardService->updateQueueStatus($params);
        $this->assertEquals($response['status'], 'success');
    }
}
