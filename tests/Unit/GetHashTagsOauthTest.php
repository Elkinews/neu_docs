<?php


namespace Tests\Unit;

use App\Services\ViewDocumentService;
use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class GetHashTagsOauthTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;
    protected $viewDocumentService;

    public function setUp(): void
    {
        parent::setUp();
        $this->viewDocumentService = resolve(ViewDocumentService::class);
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_get_hashtags()
    {

        /**
         * params can contain :
         * profileId (req)
         * docId (req)
         * pieceId (req)
         * count
         * single
         * pagination
         * page
         * limit
         */

        //only this entry has hashtags, so search for it.
        $fieldsParam = [
            ["field_name"=>"trade_name",
                "field_value"=>"H1_TEST10.PDF"]
        ];
        $params = ["profile_id"=>1,
            "search_fields"=>$fieldsParam,
            "page"=>0,
            "page_size"=>20,
            "strict"=>true];

        $pieceID = 1;
        $profileID = 1;

        $res = $this->dashboardService->searchImages($params);

        $images = $res["body"]->data->search_results->images;
        $id = $images[0]->image_id;

        $params = [
            "profileId" => $profileID,
            "docId"=>$id,
            "pieceId"=>$pieceID
        ];

        $res = $this->viewDocumentService->GetHashtagsOauth($params);
        $this->assertEquals($res['status'], 'success');

    }

}
