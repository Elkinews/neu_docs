<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use App\Services\NdeService;
use Tests\TestCase;

class TransferTabTest extends TestCase
{
    protected $dashboardService;
    protected $ndeService;

    public function setUp() : void{
        parent::setUp();
        $this->dashboardService = resolve(DashboardService::class);
        $this->ndeService = resolve(NdeService::class);
        $this->ndeService->oauthPassword(config('nde.test_username'), config('nde.test_password'));
    }

    public function test_transfer_tab()
    {
        $tabsParam = '[{
	\"type\": \"merge\",
	\"sourceID\": \"OGtNOG5DQVdtYk1YdVhiT2RjcS9mZz09X1_y8lIgJO-rYLXRDLnzjU0E\",
	\"tarID\": \"NGcvQ21uVXB2bGtmbFM0VUhhVlNSUT09X19118_uFostDsclstE8mZTF\",
	\"name2\": \"Main\",
	\"boxType\": \"B\",
	\"tabs\": [{
		\"sourceID\": \"bTZuZm91SHMxOTBTL1ozWklhM1I1QT09X1-w7VvNjLUrQ695kt7RqpUQ\",
		\"type\": \"move\",
		\"tarID\": \"NGcvQ21uVXB2bGtmbFM0VUhhVlNSUT09X19118_uFostDsclstE8mZTF\",
		\"na…ame2\": \"pokemon\",
		\"boxType\": \"B\"
	}]
}]';

        $fieldsParam = '{
			"document_number": "123456789"
		}';
        $data['fields'] = $fieldsParam;
        $data['profile'] = 1;
        $data['docID'] = "UXZUY2lMQ3g2N1pOOGR1TzhuWTErQT09X19qJoA8uxWDR9KTcT2XmZSY";
        $data['tarID'] = "NGcvQ21uVXB2bGtmbFM0VUhhVlNSUT09X19118_uFostDsclstE8mZTF";
        $data['sourceID'] = "OGtNOG5DQVdtYk1YdVhiT2RjcS9mZz09X1_y8lIgJO-rYLXRDLnzjU0E";
        $data['desID'] = 2;
        $data['targetDocID'] = "NGNsNHdncmRoTWlMWUZNNDVXWFpzZz09X18E328k6GBjUteHwrLGpBlR";
        $data['allTransfer'] = '{"allTransfer": true}';
        $param = [[
            "type" => "merge",
            "sourceID" => "OGtNOG5DQVdtYk1YdVhiT2RjcS9mZz09X1_y8lIgJO-rYLXRDLnzjU0E",
            "tarID" => "NGcvQ21uVXB2bGtmbFM0VUhhVlNSUT09X19118_uFostDsclstE8mZTF",
            "name2" => "Main",
            "boxType"=> "B",
            "tabs"=> [[
                "sourceID"=> "bTZuZm91SHMxOTBTL1ozWklhM1I1QT09X1-w7VvNjLUrQ695kt7RqpUQ",
                "type"=> "move",
                "tarID"=> "NGcvQ21uVXB2bGtmbFM0VUhhVlNSUT09X19118_uFostDsclstE8mZTF",
                "na…ame2"=> "pokemon",
                "boxType"=> "B"
            ]]
        ]];

        $data['tabs'] = json_encode($param);
        $response = $this->dashboardService->transferTab($data);
print_r($response);
        $this->assertEquals($response['status'], 'success');
    }
}
