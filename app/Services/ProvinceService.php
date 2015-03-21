<?php

namespace App\Services;

class ProvinceService
{
    private $ndeService;
    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function getCollectedProvincePermissions($params)
    {
        $responseReturn = $this->ndeService->callNde('GetCollectedProvincePermissionsOauth', 'get', $params);
        return $responseReturn;
    }
}
