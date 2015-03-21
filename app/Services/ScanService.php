<?php

namespace App\Services;

class ScanService
{
    private $ndeService;

    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function viewScanOptionsOauth($params){
        $responseReturn = $this->ndeService->callNde('ViewScanOptionsOauth', 'get', $params);
        return $responseReturn;
    }

    public function getPredefinedBookmarkOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetPredefinedBookmarkOauth', 'get', $params);
        return $responseReturn;
    }
}
