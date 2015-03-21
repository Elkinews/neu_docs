<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class UploadFileTest extends TestCase
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
    public function test_upload_file()
    {
        $fieldsParam = [ ['field_name' => 'state', 'field_value' => 'TX']];
        $params = ['profile_id' => 1, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];


        $response = $this->dashboardService->searchImages($params);

        $id = $response['body']->data->search_results->images[0]->image_id;
        $data['json'] = json_encode([
            'scan' => 'false',
            'docId' => $id,
            'profileId' => 1,
            'boxType' => 'B']);
        $data['neu_file'] = getcwd() . '/tests/file.pdf';

        $response = $this->dashboardService->uploadFile($data);

        $this->assertEquals($response['status'], 'success');
    }
}
