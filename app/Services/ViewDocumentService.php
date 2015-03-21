<?php

namespace App\Services;

class ViewDocumentService
{
    private $ndeService;

    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function getPieceBoxParts($params){
        $responseReturn = $this->ndeService->callNde('GetPieceBoxPartsOauth', 'get', $params);
        return $responseReturn;
    }

    public function appendCustomView($params){
        $responseReturn = $this->ndeService->callNde('AppendCustomViewOauth', 'get', $params);
        return $responseReturn;
    }

    public function getImageByDocId($params){
        $responseReturn = $this->ndeService->callNde('GetImageByDocIdOauth', 'get', $params);
        return $responseReturn;
    }

    public function createCustomView($params){
        $responseReturn = $this->ndeService->callNde('AppendCustomViewOauth', 'post', $params);
        return $responseReturn;
    }

    public function sortTabs($params){
        $responseReturn = $this->ndeService->callNde('SortTabsOauth', 'post', $params);
        return $responseReturn;
    }

    public function getCustomViewPage($param){
        $responseReturn = $this->ndeService->callNde('GetCustomViewPageOauth', 'get', $param);
        return $responseReturn;
    }

    public function getCustomViewNames(){
        $responseReturn = $this->ndeService->callNde('GetCustomViewNamesOauth', 'get');
        return $responseReturn;
    }

    public function getGetAllowDeleteInd($param){
        $responseReturn = $this->ndeService->callNde('GetAllowDeleteIndOauth', 'get', $param);
        return $responseReturn;
    }

    public function getAllTabs($params){
        $responseReturn = $this->ndeService->callNde('GetAllTabsOauth', 'get', $params);
        return $responseReturn;
    }

    public function GetAllowEditIndWithImageArrayParam($params) {
        $responseReturn = $this->ndeService->callNde('GetAllowEditIndWithImageArrayParamOauth', 'get', $params);
        return $responseReturn;
    }

    public function GetAllowNewContentIndOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetAllowNewContentIndOauth', 'get', $params);
        return $responseReturn;
    }

    public function getPiecesByDocIdOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetPiecesByDocIdOauth', 'get', $params);
        return $responseReturn;
    }

    public function getTabFilesOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetTabFilesOauth', 'get', $params);
        return $responseReturn;
    }

    public function copyToMyDocumentsOauth($params) {
      $responseReturn = $this->ndeService->callNde('CopyToMyDocumentsOauth', 'post', $params);
      return $responseReturn;
    }

    public function getTabFilesHistoryOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetTabFilesHistoryOauth', 'get', $params);
        return $responseReturn;
    }

    public function updateDocumentPieceOauth($params) {
        $responseReturn = $this->ndeService->callNde('UpdateDocumentPieceOauth', 'post', $params);
        return $responseReturn;
    }

    public function getLinksOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetLinksOauth', 'get', $params);
        return $responseReturn;
    }

    public function cancelLinkOauth($params) {
        $responseReturn = $this->ndeService->callNde('CancelLinkOauth', 'post', $params);
        return $responseReturn;
    }

    public function addTempLinkOauth($params) {
        $responseReturn = $this->ndeService->callNde('AddTempLinkOauth', 'post', $params);
        return $responseReturn;
    }
    
    public function getFileLockStatusOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetFileLockStatusOauth', 'get', $params);
        return $responseReturn;
    }
    
    public function lockFileOauth($params) {
        $responseReturn = $this->ndeService->callNde('LockFileOauth', 'post', $params);
        return $responseReturn;
    }

    public function getFolderPredefinedOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetFolderPredefinedOauth', 'get', $params);
        return $responseReturn;
    }

    public function addFolderOauth($params) {
        $responseReturn = $this->ndeService->callNde('AddFolderOauth', 'post', $params);
        return $responseReturn;
    }

    public function deleteFolderOauth($params) {
        $responseReturn = $this->ndeService->callNde('DeleteFolderOauth', 'post', $params);
        return $responseReturn;
    }

    public function renameFolderOauth($params) {
        $responseReturn = $this->ndeService->callNde('RenameFolderOauth', 'post', $params);
        return $responseReturn;
    }

    public function updatePieceFilenameOauth($params) {
        $responseReturn = $this->ndeService->callNde('UpdatePieceFilenameOauth', 'post', $params);
        return $responseReturn;
    }
}
