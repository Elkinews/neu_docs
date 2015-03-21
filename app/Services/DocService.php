<?php

namespace App\Services;

class DocService
{
    private $ndeService;

    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function getDocMimeTypeOauth($params){
        $responseReturn = $this->ndeService->callNde('GetDocMimeTypeOauth', 'get', $params);
        return $responseReturn;
    }
}