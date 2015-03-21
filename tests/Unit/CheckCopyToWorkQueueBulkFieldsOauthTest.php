<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckCopyToWorkQueueBulkFieldsOauthTest extends TestCase
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
    public function test_check_copy_to_work_queue_bulk_fields_oauth()
    {
        $this->ndeService->oauthPassword(env('TEST_USERNAME'), env('TEST_PASSWORD'));

        $data = [
            "profile" => 1,
            "docIDs[0]" => "ljp8gS3PmxA.",
            "docIDs[1]" => "kTM5vE6gvhE.",
            'fields[file_number]' => "8675309",
            'targetProfileID' => 1
        ];

        $response = $this->dashboardService->checkCopyToWorkQueueBulkFieldsOauth($data);

        $this->assertEquals($response['status'], 'success');
    }
}
