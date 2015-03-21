<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplyActionCustomRuleOauthTest extends TestCase
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

    public function test_apply_action_custom_rule_oauth()
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
            "action_type" => "legal_hold",
            "rule" => "SET",
            "docID" => $docId
        ];

        $response = $this->dashboardService->applyActionCustomRuleOauth($data);

        $this->assertEquals($response['status'], 'success');
    }
}
