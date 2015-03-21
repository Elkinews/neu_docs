<?php

namespace Tests\Unit;

use App\Services\ViewDocumentService;
use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetCustomViewPageTest extends TestCase
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

    public function test_get_custom_view_page()
    {

        $response = $this->viewDocumentService->getCustomViewNames();
        $vname = $response['body']->data->data->names[0];


        $id = $response['body']->data->data->tabs->$vname[0]->id;

        $params = [
            "docId"=>[$id]
        ];
        $res = $this->viewDocumentService->getCustomViewPage($params);

        $this->assertEquals($res['status'], 'success');
    }

}
