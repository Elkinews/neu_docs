<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class LockDocOauth extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
    }

    /**
     * A LockDocOauth unit test example.
     *
     * @return void
     */
    public function test_lock_doc()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));
        $fieldsParam = [['field_name' => 'file_number', 'field_value' => '123'], ['field_name' => 'city', 'field_value' => 'ccc']];
        $params = ['profile_id' => 1, 'search_fields' => $fieldsParam,
            'page' => 0, 'page_size' => 20, 'strict' => true];


        $response = $this->dashboardService->searchImages($params);

        $id = $response['body']->data->search_results->images[0]->image_id;
        $params = [
            'profile' => 1,
            'ownerId' => 1110,
            'docIDs' => [$id]
        ];

        $response = $this->dashboardService->getUnlockDoc($params);
        $response = $this->dashboardService->getLockDoc($params);
        $this->assertEquals($response['status'], 'success');
    }
}
