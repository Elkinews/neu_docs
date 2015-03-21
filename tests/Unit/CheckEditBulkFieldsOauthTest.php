<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckEditBulkFieldsOauthTest extends TestCase
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

    /**
     * @return void
     */
    public function test_check_edit_bulk_fields_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));

        $fieldsParam = [
            ['field_name' => 'file_number', 'field_value' => '11'],
        ];

        $params = [
            'profile_id' => 1,
            'search_fields' => $fieldsParam,
            'page' => 0,
            'page_size' => 20,
            'strict' => true
        ];

        $response = $this->dashboardService->searchImages($params);

        $data = [
            "profile" => 1,
            "docIDs[0]" => $response['body']->data->search_results->images[0]->image_id,
            "docIDs[1]" => $response['body']->data->search_results->images[1]->image_id,
            'fields[field_key]' => "sort_order",
            'fields[field_value]' => 1,
        ];

        $response = $this->dashboardService->checkEditBulkFieldsOauth($data);

        $this->assertEquals($response['status'], 'success');
    }
}
