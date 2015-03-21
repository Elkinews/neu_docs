<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class SearchImagesTest extends TestCase
{

    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_search_images(){
        $fieldsParam = [['field_name' => 'file_number', 'field_value' => '123'], ['field_name' => 'city', 'field_value' => 'ccc']];
        $params = ['profile_id' => 1, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];
        $response = $this->dashboardService->searchImages($params);
        $this->assertEquals($response['status'], 'success');
        $body = $response['body'];
        $this->assertObjectHasAttribute('images', $response['body']->data->search_results);
        $this->assertEquals($response['body']->data->meta->num_loaded, count($response['body']->data->search_results->images));
    }
}
