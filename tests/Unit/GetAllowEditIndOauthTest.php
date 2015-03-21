<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllowEditIndOauthTest extends TestCase
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
     * A GetUploadFieldsOauth unit test example.
     *
     * @return void
     */
    public function test_get_allow_edit_ind_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));

        $fieldsParam = [
            ['field_name' => 'file_number', 'field_value' => '123'],
            ['field_name' => 'city', 'field_value' => 'ccc']
        ];

        $params = [
            'profile_id' => 1,
            'search_fields' => $fieldsParam,
            'page' => 0,
            'page_size' => 20,
            'strict' => true
        ];

        $response = $this->dashboardService->searchImages($params);

        $id = $response['body']->data->search_results->images[0]->image_id;
        $params = [
            'profileId' => 1,
            'imageId' => $id
        ];

        $response = $this->dashboardService->getAllowEditIndOauth($params);
        $this->assertEquals($response['status'], 'success');
    }
}
