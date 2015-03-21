<?php

namespace App\Services;

class ProfileService
{
    private $ndeService;
    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function getProfilesOauth($params)
    {
        $responseReturn = $this->ndeService->callNde('GetProfilesOauth', 'get', $params);
        return $responseReturn;
    }
}
