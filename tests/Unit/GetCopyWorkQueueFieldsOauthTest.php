<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetCopyWorkQueueFieldsOauthTest extends TestCase
{
    use RefreshDatabase;

    protected $dashboardService;
    protected $ndeService;

    public function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
    }

    public function test_get_copy_work_queue_fields_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));

        $profileId = 1;
        $fieldsParam = [[
            'field_name' => 'state',
            'field_value' => 'TX'
        ]];

        $params = [
            'profile_id' => $profileId,
            'search_fields' => $fieldsParam,
            'page' => 0,
            'page_size' => 20,
            'strict' => true
        ];

        $response = $this->dashboardService->searchImages($params);

        $docId = $response['body']->data->search_results->images[0]->image_id;

        $data = [
            "profile" => $profileId,
            "docID" => $docId,
            "unused_tab_selected" => "0",
            "unused_tab_selected_names[0]" => "123",
            "unused_tab_selected_names[1]" => "456",
        ];

        $response = $this->dashboardService->getCopyWorkQueueFieldsOauth($data);

        $this->assertEquals($response['status'], 'success');
    }
}
