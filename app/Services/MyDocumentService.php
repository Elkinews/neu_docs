<?php
namespace App\Services;
class MyDocumentService {
    private $ndeService;
    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function getDownloadOauth($params){
      $responseReturn = $this->ndeService->callNde('GetDownloadOauth', 'get', $params);
      return $responseReturn;
    }

    public function deleteDownloadOauth($params){
      $responseReturn = $this->ndeService->callNde('DeleteDownloadOauth', 'delete', $params);
      return $responseReturn;
    }

    public function getSharableAccountsOauth($params){
      $responseReturn = $this->ndeService->callNde('GetSharableAccountsOauth', 'get', $params);
      return $responseReturn;
    }

    public function shareMyDocsOauth($params){
      $responseReturn = $this->ndeService->callNde('ShareMyDocsOauth', 'post', $params);
      return $responseReturn;
    }

}
