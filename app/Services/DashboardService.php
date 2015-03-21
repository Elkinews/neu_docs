<?php

namespace App\Services;

class DashboardService
{
    private $ndeService;

    public function __construct(NdeService $ndeService) {
        $this->ndeService = $ndeService;
    }

    public function getProfiles(){
        $params = [
            'modules' => ['parentprofile', 'esd'],
        ];
        $responseReturn = $this->ndeService->callNde('GetProfilesOauth', 'get', $params);
        return $responseReturn;
    }

    public function getTagsByTabID($params){
        $responseReturn = $this->ndeService->callNde('GetTagsByTabIDOauth', 'get', $params);
        return $responseReturn;
    }

    public function getSortRecordOrder($params){
        $responseReturn = $this->ndeService->callNde('GetSortRecordOrderOauth', 'get', $params);
        return $responseReturn;
    }

    public function getScannerCount($params){
        $responseReturn = $this->ndeService->callNde('GetScannerCountOauth', 'get', $params);
        return $responseReturn;
    }

    public function getProfileinfo($params){
        $responseReturn = $this->ndeService->callNde('GetProfileinfoOauth', 'get', $params);
        return $responseReturn;
    }

    public function createDocument($params){
        $responseReturn = $this->ndeService->callNde('CreateDocumentOauth', 'postBody', $params);
        return $responseReturn;
    }

    public function sortRecordOrder($params){
        $responseReturn = $this->ndeService->callNde('SortRecordOrderOauth', 'post', $params);
        return $responseReturn;
    }

    public function queueDownload($params){
        $responseReturn = $this->ndeService->callNde('QueueDownloadOauth', 'post', $params);
        return $responseReturn;
    }

    public function getDownloads($params){
        $responseReturn = $this->ndeService->callNde('GetDownloads', 'post', $params);
        return $responseReturn;
    }

    public function exportToCSV($params){
        $responseReturn = $this->ndeService->callNde('ExportToCSVOauth', 'post', $params);
        return $responseReturn;
    }

    public function mergeRecord($params){
        $responseReturn = $this->ndeService->callNde('MergeRecordOauth', 'post', $params);
        return $responseReturn;
    }

    public function updateQueueStatus($params){
        $responseReturn = $this->ndeService->callNde('UpdateQueueStatusOauth', 'post', $params);
        return $responseReturn;
    }

    public function updateImageStatus($params){
        $responseReturn = $this->ndeService->callNde('UpdateImageStatusOauth', 'post', $params);
        return $responseReturn;
    }

    public function bulkEditDocFields($params){
        $responseReturn = $this->ndeService->callNde('BulkEditDocFieldsOauth', 'post', $params);
        return $responseReturn;
    }

    public function bulkCopyToWorkqueue($params){
        $responseReturn = $this->ndeService->callNde('BulkCopyToWorkqueueOauth', 'post', $params);
        return $responseReturn;
    }

    public function getWatchlist($param){
        $responseReturn = $this->ndeService->callNde('GetWatchlistOauth', 'get', $param);
        return $responseReturn;
    }

    public function addUserWatchlist($params){
        $responseReturn = $this->ndeService->callNde('AddUserWatchlistOauth', 'post', $params);
        return $responseReturn;
    }

    public function deleteUserWatchlist($params){
        $responseReturn = $this->ndeService->callNde('DeleteUserWatchlistOauth', 'delete', $params);
        return $responseReturn;
    }

    public function deleteDocument($params){
        $responseReturn = $this->ndeService->callNde('DeleteDocumentOauth', 'delete', $params);
        return $responseReturn;
    }

    public function deletePieceOauth($params){
        $responseReturn = $this->ndeService->callNde('DeletePieceOauth', 'delete', $params);
        return $responseReturn;
    }

    public function searchImages($params)
    {
        $responseReturn = $this->ndeService->callNde('SearchImagesOauth', 'postBody', $params);
        return $responseReturn;
    }
    public function getActions($param){
        $responseReturn = $this->ndeService->callNde('GetActionsOauth', 'get', $param);
        return $responseReturn;
    }

    public function getBulkActions($param){
        $responseReturn = $this->ndeService->callNde('GetBulkActionsOauth', 'get', $param);
        return $responseReturn;
    }

    public function editDocIndexingFields($params)
    {
        $responseReturn = $this->ndeService->callNde('EditDocIndexingFieldsOauth', 'putBody', $params);
        return $responseReturn;
    }


    public function getLastActivity($param){
        $responseReturn = $this->ndeService->callNde('GetLastActivityOauth', 'get', $param);
        return $responseReturn;
    }

    public function docInProcessing($param){
        $responseReturn = $this->ndeService->callNde('DocInProcessingOauth', 'get', $param);
        return $responseReturn;
    }

    public function getPossibleSearchData($param){
        $responseReturn = $this->ndeService->callNde('GetPossibleSearchDataOauth', 'get', $param);
        return $responseReturn;
    }

    public function viewScanPlugin($param){
        $responseReturn = $this->ndeService->callNde('ViewScanPluginOauth', 'get', $param);
        return $responseReturn;
    }

    public function getUploadFields($param){
        $responseReturn = $this->ndeService->callNde('GetUploadFieldsOauth', 'get', $param);
        return $responseReturn;
    }
    public function getControls($param){
        $responseReturn = $this->ndeService->callNde('GetControlsOauth', 'get', $param);
        return $responseReturn;
    }
    public function getWorkqueueControls($param){
        $responseReturn = $this->ndeService->callNde('GetWorkqueueControlsOauth', 'get', $param);
        return $responseReturn;
    }
    public function getDoubleEntry($param){
        $responseReturn = $this->ndeService->callNde('GetDoubleEntryOauth', 'get', $param);
        return $responseReturn;
    }
    public function getSearchHistory($param){
        $responseReturn = $this->ndeService->callNde('GetSearchHistoryOauth', 'get', $param);
        return $responseReturn;
    }

    public function uploadFile($param){
        $responseReturn = $this->ndeService->callNde('UploadFileOauth', 'postFile', $param);
        return $responseReturn;
    }

    public function getLockDoc($param){
        $responseReturn = $this->ndeService->callNde('LockDocOauth', 'post', $param);
        return $responseReturn;
    }

    public function getUnlockDoc($param){
        $responseReturn = $this->ndeService->callNde('UnlockDocOauth', 'post', $param);
        return $responseReturn;
    }

    public function getCheckFieldHint($param){
        $responseReturn = $this->ndeService->callNde('CheckFieldHintOauth', 'get', $param);
        return $responseReturn;
    }

    public function transferTab($param){
        $responseReturn = $this->ndeService->callNde('TransferTabOauth', 'post', $param);
        return $responseReturn;
    }

    public function createUserWatchlist($param){
        $responseReturn = $this->ndeService->callNde('CreateUserWatchlistOauth', 'postBody', $param);
        return $responseReturn;
    }

    public function getRolesForUser(){
        $responseReturn = $this->ndeService->callNde('GetRolesForUserOauth', 'get', []);
        return $responseReturn;
    }

    public function getLoginUserDetailsOauth(){
        $responseReturn = $this->ndeService->callNde('GetLoginUserDetailsOauth', 'get', []);
        return $responseReturn;
    }

    public function getEndpoints(){
        $responseReturn = $this->ndeService->callNde('GetEndpointsOauth', 'get', []);
        return $responseReturn;
    }


    public function copyToWorkQueue($params){
        $responseReturn = $this->ndeService->callNde('CopyToWorkqueueOauth', 'post', $params);
        return $responseReturn;
    }

    public function getVersion(){
        $responseReturn = $this->ndeService->callNde('GetVersionOauth', 'get', []);
        return $responseReturn;
    }

    public function promoteSelectedTabOauth($params) {
        $responseReturn = $this->ndeService->callNde('PromoteSelectedTabOauth', 'post', $params);
        return $responseReturn;
    }

    public function getAllowEditIndOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetAllowEditIndOauth', 'get', $params);
        return $responseReturn;
    }

    public function getPageNotesOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetPageNotesOauth', 'get', $params);
        return $responseReturn;
    }

    public function savePageNoteOauth($params) {
        $responseReturn = $this->ndeService->callNde('SavePageNoteOauth', 'post', $params);
        return $responseReturn;
    }

    public function customOnDemandReportOauth($params) {
        $responseReturn = $this->ndeService->callNde('CustomOnDemandReportOauth', 'get', $params);
        return $responseReturn;
    }

    public function getAllQuestionsOauth($params = []) {
        $responseReturn = $this->ndeService->callNde('GetAllQuestionsOauth', 'get', $params);
        return $responseReturn;
    }

    public function allowCopyWorkQueueOauth($params) {
        $responseReturn = $this->ndeService->callNde('AllowCopyWorkQueueOauth', 'get', $params);
        return $responseReturn;
    }

    public function checkCopyToWorkQueueBulkFieldsOauth($params) {
        $responseReturn = $this->ndeService->callNde('CheckCopyToWorkQueueBulkFieldsOauth', 'post', $params);
        return $responseReturn;
    }

    public function checkEditBulkFieldsOauth($params){
        $responseReturn = $this->ndeService->callNde('CheckEditBulkFieldsOauth', 'post', $params);
        return $responseReturn;
    }

    public function checkImageFieldsOauth($params) {
        $responseReturn = $this->ndeService->callNde('CheckImageFieldsOauth', 'post', $params);
        return $responseReturn;
    }

    public function getAllTabValuesOauth($params = []) {
        $responseReturn = $this->ndeService->callNde('GetAllTabValuesOauth', 'get', $params);
        return $responseReturn;
    }

    public function analyticsOauth($params) {
        $responseReturn = $this->ndeService->callNde('AnalyticsOauth', 'post', $params);
        return $responseReturn;
    }

    public function applyActionCustomRuleOauth($params = []) {
        $responseReturn = $this->ndeService->callNde('ApplyActionCustomRuleOauth', 'post', $params);
        return $responseReturn;
    }

    public function getBookmarksOauth($params = []) {
        $responseReturn = $this->ndeService->callNde('GetBookmarksOauth', 'get', $params);
        return $responseReturn;
    }

    public function getCopyWorkQueueFieldsOauth($params = []) {
        $responseReturn = $this->ndeService->callNde('GetCopyWorkQueueFieldsOauth', 'get', $params);
        return $responseReturn;
    }

    public function getDocHistoryOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetDocHistoryOauth', 'get', $params);
        return $responseReturn;
    }

    public function checkDatafeedCompletionOauth($params) {
        $responseReturn = $this->ndeService->callNde('CheckDatafeedCompletionOauth', 'get', $params);
        return $responseReturn;
    }

    public function getViewRecordOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetViewRecordOauth', 'get', $params);
        return $responseReturn;
    }

	public function requestDocFromStorageOauth($params) {
        $responseReturn = $this->ndeService->callNde('RequestDocFromStorageOauth', 'post', $params);
        return $responseReturn;
    }

	public function checkProfilePageOrderOauth($params) {
        $responseReturn = $this->ndeService->callNde('CheckProfilePageOrderOauth', 'get', $params);
        return $responseReturn;
    }

	public function getAllowNewContentIndOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetAllowNewContentIndOauth', 'get', $params);
        return $responseReturn;
    }

	public function getProfileInfoOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetProfileInfoOauth', 'get', $params);
        return $responseReturn;
    }

	public function getAllTabs($params){
        $responseReturn = $this->ndeService->callNde('GetAllTabsOauth', 'get', $params);
		return $responseReturn;
	}

    public function getWorkflowProfilesOauth() {
        $responseReturn = $this->ndeService->callNde('GetWorkflowProfilesOauth', 'get', []);
        return $responseReturn;
    }

    public function queueDownloadOauth($params) {
        $responseReturn = $this->ndeService->callNde('QueueDownloadOauth', 'post', $params);
        return $responseReturn;
    }

	public function getDocTypeOauth($params){
        $responseReturn = $this->ndeService->callNde('GetDocTypeOauth', 'get', $params);
        return $responseReturn;
    }

    public function updateImageStatusOauth($params){
        $responseReturn = $this->ndeService->callNde('UpdateImageStatusOauth', 'post', $params);
        return $responseReturn;
    }
    
    public function getModulesOauth(){
        $responseReturn = $this->ndeService->callNde('GetModulesOauth', 'get', []);
        return $responseReturn;
    }
    
}
