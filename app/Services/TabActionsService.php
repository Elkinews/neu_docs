<?php

namespace App\Services;

class TabActionsService
{
    private $ndeService;

    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function deletePredefinedTabOauth($params)
    {
        $responseReturn = $this->ndeService->callNde('DeletePredefinedTabOauth', 'delete', $params);
        return $responseReturn;
    }

    public function changeTabNameOauth($params)
    {
        $responseReturn = $this->ndeService->callNde('ChangeTabNameOauth', 'post', $params);
        return $responseReturn;
    }

    public function deleteTab($params)
    {
        $responseReturn = $this->ndeService->callNde('DeleteTabOauth', 'delete', $params);
        return $responseReturn;
    }

    public function renameTab($params)
    {
        $responseReturn = $this->ndeService->callNde('RenameTabOauth', 'post', $params);
        return $responseReturn;
    }

    public function addTab($params)
    {
        $responseReturn = $this->ndeService->callNde('AddTabOauth', 'post', $params);
        return $responseReturn;
    }
}
