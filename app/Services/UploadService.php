<?php

namespace App\Services;

class UploadService
{
    private $ndeService;

    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function uploadFileMetadataOauth($params)
    {
        $responseReturn = $this->ndeService->callNde('UploadFileMetadataOauth', 'post', $params);
        return $responseReturn;
    }

    /**
     * @param array $params
     * @return JsonResponse|Array
     */
    public function getAnnotationTypesOauth($params)
    {
        $responseReturn = $this->ndeService->callNde('GetAnnotationTypesOauth', 'get', $params);
        return $responseReturn;
    }
}
