<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class UploadFileScanTest extends TestCase
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
     * A UploadFileScanTest unit test example.
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
            'scan' => 'true',
            'docId' => $id,
            'profileId' => 1,
            'serialNumber' => '27XHL30272',
            'firstUpload' => 'true',
            'numPages' => '1',
            'longitude' => '12.34',
            'latitude' => '12.34',
            'rollerCount' => '123',
            'preRollerCount' => '123',
            'boxType' => 'B']);
        $data['neu_file'] = getcwd() . '/tests/file.pdf';

        $response = $this->dashboardService->uploadFile($data);

        $this->assertEquals($response['status'], 'success');
    }
}
