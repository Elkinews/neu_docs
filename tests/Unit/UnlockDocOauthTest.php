<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class UnlockDocOauthTest extends TestCase
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
     * A LockDocOauth unit test example.
     *
     * @return void
     */
    public function test_unlock_doc()
    {
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
        $response = $this->dashboardService->getLockDoc($params);
        $response = $this->dashboardService->getUnlockDoc($params);
        $this->assertEquals($response['status'], 'success');
    }
}
