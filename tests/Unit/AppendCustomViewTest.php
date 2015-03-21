<?php

namespace Tests\Unit;

use App\Services\ViewDocumentService;
use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class AppendCustomViewTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;
    protected $viewDocumentService;

    public function setUp() : void{
        parent::setUp();
        $this->viewDocumentService = resolve(ViewDocumentService::class);
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_append_custom_view_()
    {
        $fieldsParam = [ ['field_name' => 'state', 'field_value' => 'TX']];
        $params = ['profile_id' => 1, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];


        $response = $this->dashboardService->searchImages($params);

        $docId = $response['body']->data->search_results->images[0]->image_id;
        $response = $this->viewDocumentService->getCustomViewNames();
        $vname = $response['body']->data->data->names[0];
        $tabId = $response['body']->data->data->tabs->$vname[0]->id;

        $params = ['profileId' => 1, 'docId' => $docId];
        $response = $this->viewDocumentService->getImageByDocId($params);
        $pageId = $response['body']->data->image[0]->piece[0];

        $params = [
            'profile' => 1,
            'docID' => $docId,
            "pageIDs[]"=> $pageId,
            "name"=> $vname,
            "tabID"=> $tabId
        ];
        $response = $this->viewDocumentService->appendCustomView($params);
        $this->assertEquals($response['status'], 'success');
    }

}
