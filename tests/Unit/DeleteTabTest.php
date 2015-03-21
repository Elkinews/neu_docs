<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use App\Services\ViewDocumentService;
use Tests\TestCase;

class DeleteTabTest extends TestCase
{
    protected $dashboardService;
    protected $viewDocumentService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->viewDocumentService = resolve(ViewDocumentService::class);
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_get_tabs()
    {
        $fieldsParam = [['field_name' => 'file_number', 'field_value' => '123'], ['field_name' => 'city', 'field_value' => 'ccc'],
            ['field_name' => 'trade_name', 'field_value' => 'ttt']];
        $params = ['profile_id' => 1, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];
        $response = $this->dashboardService->searchImages($params);
        $docId = $response['body']->data->search_results->images[0]->image_id;
        $params = ['profileId' => 1, 'docId' => $docId, 'name' => 'newtab' . time(), 'boxType' => 'B', 'predefined' => false];
        $response = $this->viewDocumentService->addTab($params);
        $newTabId = $response['body']->data->tabId;
        $params = ['profileId' => 1, 'docId' => $newTabId];
        $response = $this->viewDocumentService->deleteTab($params);
        $this->assertEquals($response['status'], 'success');
    }
}
