<?php

namespace Tests\Unit;

use App\Services\ViewDocumentService;
use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class SortTabsTest extends TestCase
{
    protected $viewDocumentService;
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->viewDocumentService = resolve(ViewDocumentService::class);
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_sort_tabs()
    {
        $fieldsParam = [['field_name' => 'file_number', 'field_value' => '123'], ['field_name' => 'city', 'field_value' => 'ccc'],
            ['field_name' => 'trade_name', 'field_value' => 'sortrecord']];
        $params = ['profile_id' => 1, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];
        $response = $this->dashboardService->searchImages($params);
        $id = $response['body']->data->search_results->images[0]->image_id;
        $params = ['profileId' => 1, 'docId' => $id];
        $response = $this->viewDocumentService->getAllTabs($params);
        $tabs = $response['body']->data->tabs->Document->tabs;
        $tabsArr = [];
        foreach($tabs as $tab){
            $tabsArr[] = $tab->id;
        }
        $params = ['profileId' => 1, 'docIds' => $tabsArr];
        $response = $this->viewDocumentService->sortTabs($params);
        $this->assertEquals($response['status'], 'success');

    }
}
