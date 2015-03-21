<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class AddUserWatchlistTest extends TestCase
{


    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_add_user_watchlist(){
        $params = ['profileId' => 1, 'docId' => 'cWxicWVWbCtrSzFsaWMxbGI0elF0dz09X1-p7D0RPtmHUEGydJVnq6_U', 'action' => 'DOC_UPLOAD'];
        $response = $this->dashboardService->addUserWatchlist($params);
        if ($response['status'] === 'fail'){
            $message = $response['message'];
        }
        else {
            $message = $response['body']->message;
        }
        $this->assertContains($message, ['Added successfully to WatchList', 'A watchlist item with these fields already exists']);
        if ($response['status'] === 'success') {
            $paramsDelete = ['profileId' => 1, 'groupHash' => $response['body']->data->group_hash];
            $responseDelete = $this->dashboardService->deleteUserWatchlist($paramsDelete);
            $this->assertEquals($responseDelete['status'], 'success');
            $this->assertEquals($responseDelete['body']->message, 'User watchlist deleted');
        }
    }
}
