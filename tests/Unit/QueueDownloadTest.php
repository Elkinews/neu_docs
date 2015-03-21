<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class QueueDownloadTest extends TestCase
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
    public function test_queue_download()
    {
        $fieldsParam = [ ['field_name' => 'state', 'field_value' => 'TX']];
        $params = ['profile_id' => 1, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];


        $response = $this->dashboardService->searchImages($params);

        $id1 = $response['body']->data->search_results->images[0]->image_id;
        $id2 = $response['body']->data->search_results->images[1]->image_id;
        $data['profile'] = 1;
        $data['imageIDs'] = [$id1, $id2];
        $data['status'] = 'PROCESSING';
        $data['group'] = "true";

        $response = $this->dashboardService->queueDownload($data);

        $this->assertEquals($response['status'], 'success');
    }
}
