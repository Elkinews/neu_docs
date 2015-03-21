<?php

namespace Tests\Unit;

use App\Services\ViewDocumentService;
use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class ViewScanPluginTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_view_scan_plugin_oauth()
    {
        $fieldsParam = [
            ["field_name"=>"state",
                "field_value"=>"TX"]
        ];
        $params = ["profile_id"=>1,
            "search_fields"=>$fieldsParam,
            "page"=>0,
            "page_size"=>20,
            "strict"=>true];
        $res = $this->dashboardService->searchImages($params);
        $id = $res['body']->data->search_results->images[0]->image_id;

        $params = [
            "profile"=>1,
            "docID"=>"$id"
        ];

        $res = $this->dashboardService->viewScanPlugin($params);
        $this->assertEquals($res['status'], 'success');
    }

}
