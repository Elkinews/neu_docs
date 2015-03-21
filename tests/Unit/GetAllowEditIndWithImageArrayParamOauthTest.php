<?php

namespace Tests\Unit;

use App\Services\ViewDocumentService;
use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetAllowEditIndWithImageArrayParamOauthTest extends TestCase
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
    
    public function test_get_allow_edit_ind_with_image_array_param_oauth()
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
            "imageArr"=>[$id]
        ];
         
        $res = $this->viewDocumentService->GetAllowEditIndWithImageArrayParam($params);
        
        $this->assertEquals($res['status'], 'success');
    }

}
