<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class MergeRecordTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_merge_record()
    {
        $fieldsParam = [['field_name' => 'file_number', 'field_value' => '123'], ['field_name' => 'city', 'field_value' => 'ccc'],
            ['field_name' => 'trade_name', 'field_value' => 'ttt']];
        $params = ['profile_id' => 1, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];
        $response = $this->dashboardService->searchImages($params);
        $toId = $response['body']->data->search_results->images[0]->image_id;
        $tradeName = 'merge' . time();
        $params = ['profile_id' => 1, 'fields' => ['file_number' => '123', 'city' => 'ccc', 'trade_name' => $tradeName]];
        $response = $this->dashboardService->createDocument($params);
        $fieldsParam = [['field_name' => 'file_number', 'field_value' => '123'], ['field_name' => 'city', 'field_value' => 'ccc'],
            ['field_name' => 'trade_name', 'field_value' => $tradeName]];
        $params = ['profile_id' => 1, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];
        $response = $this->dashboardService->searchImages($params);
        $fromId = $response['body']->data->search_results->images[0]->image_id;
        $params = ['profileId' => 1, 'fromId' => $fromId, 'toId' => $toId];
        $response = $this->dashboardService->mergeRecord($params);
        $this->assertEquals($response['status'], 'success');
    }
}
