<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavePageNoteOauthTest extends TestCase
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

    public function test_save_page_note_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));

        $fieldsParam = [
            ['field_name' => 'file_number', 'field_value' => '11'],
            ['field_name' => 'city', 'field_value' => 'LAREDO\'']
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

        $pieceId = 355824;

        $data = [
            'profile' => 1,
            'pieceID' => $pieceId,
            'docID' => $id,
            'text' => 'text'
        ];

        $response = $this->dashboardService->savePageNoteOauth($data);

        $this->assertEquals($response['status'], 'success');
    }
}
