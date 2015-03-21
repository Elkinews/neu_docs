<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class ExportToCSVTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_export_to_csv()
    {
        $data['exportdata'] = ",&quot;File #&quot;,&quot;License Type&quot;,&quot;License Number&quot;,&quot;Street Number&quot;,&quot;Address&quot;,&quot;City&quot;,&quot;County Name&quot;,&quot;State&quot;,&quot;Zip&quot;,&quot;Trade Name&quot;,&quot;Court Date&quot;,&quot;Owner Name&quot;,&quot;Date&quot;,&quot;Fruit&quot;,&quot;Integer Range Search From&quot;,&quot;Integer Range Search To&quot;,&quot;Animal(s)&quot;,&quot;Car Color&quot;,&quot;Document Type&quot;,&quot;Disposition Date&quot;,&quot;Arbitrary Number&quot;
&quot;Record 1: Actions&quot;,&quot;5226&quot;,,&quot;123&quot;,,,&quot;austin&quot;,,,,&quot;3911 regression A&quot;,&quot;12/30/2020&quot;,,&quot;12/25/2020&quot;,,,,,,,,
&quot;Record 2: Actions&quot;,&quot;99909&quot;,,,,,&quot;austin&quot;,,,,&quot;big scan test&quot;,,,,,,,,,,,
&quot;Record 3: Actions&quot;,&quot;5226&quot;,&quot;GG&quot;,,,,&quot;austin&quot;,,,,&quot;393 regression&quot;,,,,,&quot;1&quot;,&quot;17&quot;,,,,&quot;10/31/2021&quot;,
&quot;Record 4: Actions&quot;,&quot;5226&quot;,,,,,&quot;austin&quot;,,,,&quot;394 regression&quot;,,,,,,,,,,,
&quot;Record 5: Actions&quot;,&quot;5226&quot;,&quot;GG&quot;,,,,&quot;austin&quot;,,,,&quot;393 regression B&quot;,,,,,,,,,,&quot;10/01/2021&quot;,
&quot;Record 6: Actions&quot;,&quot;5226&quot;,,,,,&quot;austin&quot;,,,,&quot;394 regression B&quot;,,,,,,,,,,,
&quot;Record 7: Actions&quot;,&quot;75&quot;,,,,,&quot;austin&quot;,,,&quot;78660&quot;,,,&quot;Boring bob&quot;,,,,,,,,,
&quot;Record 8: Actions&quot;,&quot;5915370&quot;,,,,,&quot;austin&quot;,,,&quot;77379&quot;,&quot;bulk download 1&quot;,&quot;07/20/2019&quot;,,&quot;07/12/2019&quot;,&quot;Oranges&quot;,,,,,,&quot;03/10/2020&quot;,
&quot;Record 9: Actions&quot;,&quot;5226&quot;,,,,,&quot;austin&quot;,,,,&quot;3911 regression C&quot;,&quot;12/30/2020&quot;,,&quot;12/25/2020&quot;,,,,,,,,
&quot;Record 10: Actions&quot;,&quot;5226&quot;,&quot;GG&quot;,,,,&quot;austin&quot;,,,,&quot;393 regression C&quot;,,,,,,,,,,&quot;10/01/2021&quot;,";
        $data['filename'] = "KS_1_austin_20220214_export";
        $response = $this->dashboardService->exportToCSV($data);
print_r($response);
        $this->assertEquals($response['status'], 'success');
    }
}
