<?php
namespace App\Services;
class AdministrationService {
    private $ndeService;

    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }


    public function getProvinces($params){
        $responseReturn = $this->ndeService->callNde('GetProvincesOauth', 'get', $params);
        return $responseReturn;
    }

    public function getHideShowColumn($params) {
        $responseReturn = $this->ndeService->callNde('GetHideShowColumnOauth', 'get', $params);
        return $responseReturn;
    }

    public function saveHideShowColumn($params) {
        $responseReturn = $this->ndeService->callNde('SaveHideShowColumnsOauth', 'post', $params);
        return $responseReturn;
    }

    public function getPredefinedTabsOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetPredefinedTabsOauth', 'get', $params);
        return $responseReturn;
    }

    public function savePredefinedTabsOauth($params) {
        $responseReturn = $this->ndeService->callNde('SavePredefinedTabsOauth', 'post', $params);
        return $responseReturn;
    }

    public function getEmailIntakeSettingsOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetEmailIntakeSettingsOauth', 'get', $params);
        return $responseReturn;
    }

    public function saveEmailIntakeOauth($params) {
        $responseReturn = $this->ndeService->callNde('SaveEmailIntakeOauth', 'post', $params);
        return $responseReturn;
    }

    public function resetAccountQuestionsOauth($params) {
        $responseReturn = $this->ndeService->callNde('ResetAccountQuestionsOauth', 'delete', $params);
        return $responseReturn;
    }

    public function accountDeleteSecurityAnswerOauth($params) {
        $responseReturn = $this->ndeService->callNde('AccountDeleteSecurityAnswerOauth', 'delete', $params);
        return $responseReturn;
    }
    
    public function getOneCaseTrainingOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetOneCaseTrainingOauth', 'get', $params);
        return $responseReturn;
    }

    public function accountForcePasswordResetOauth($params) {
        $responseReturn = $this->ndeService->callNde('AccountForcePasswordResetOauth', 'post', $params);
        return $responseReturn;
    }

    public function saveOneCaseTrainingOauth($params) {
        $responseReturn = $this->ndeService->callNde('SaveOneCaseTrainingOauth', 'post', $params);
        return $responseReturn;
    }

    public function resetUserPassOauth($params) {
        $responseReturn = $this->ndeService->callNde('ResetUserPassOauth', 'post', $params);
        return $responseReturn;
    }

    public function editUserInfoOauth($params) {
        $responseReturn = $this->ndeService->callNde('EditUserInfoOauth', 'post', $params);
        return $responseReturn;
    }

    public function createNewUserOauth($params) {
        $responseReturn = $this->ndeService->callNde('CreateNewUserOauth', 'post', $params);
        return $responseReturn;
    }
    
	public function deleteUserInfoOauth($params){
        $responseReturn = $this->ndeService->callNde('DeleteUserInfoOauth', 'delete', $params);
        return $responseReturn;
    }

    public function adminGetUsersOauth($params) {
        $responseReturn = $this->ndeService->callNde('AdminGetUsersOauth', 'get', $params);
        return $responseReturn;
    }

    public function adminGetUserDetailsOauth($params) {
        $responseReturn = $this->ndeService->callNde('AdminGetUserDetailsOauth', 'get', $params);
        return $responseReturn;
    }

    public function getPermissionControlsOauth() {
        $responseReturn = $this->ndeService->callNde('GetPermissionControlsOauth', 'get');
        return $responseReturn;
    }

    public function insertIntoUserAuditOauth($params) {
        $responseReturn = $this->ndeService->callNde('InsertIntoUserAuditOauth', 'post', $params);
        return $responseReturn;
    }

	public function createNewProvinceOauth($params){
        $responseReturn = $this->ndeService->callNde('CreateNewProvinceOauth', 'post', $params);
        return $responseReturn;
    }

	public function editProvinceInfoOauth($params){
        $responseReturn = $this->ndeService->callNde('EditProvinceInfoOauth', 'post', $params);
        return $responseReturn;
    }

	public function deleteProvinceInfoOauth($params){
        $responseReturn = $this->ndeService->callNde('DeleteProvinceInfoOauth', 'delete', $params);
        return $responseReturn;
    }

	public function getMfaQROauth($params){
        $responseReturn = $this->ndeService->callNde('GetMfaQROauth', 'get', $params);
        return $responseReturn;
    }

    public function addDatafeedEntriesOauth($params){
        $responseReturn = $this->ndeService->callNde('AddDatafeedEntriesOauth', 'post', $params);
        return $responseReturn;
    }

    public function saveTempFileOauth($params){
        $responseReturn = $this->ndeService->callNde('SaveTempFileOauth', 'post', $params);
        return $responseReturn;
    }

    public function accountDeleteSecurityAnswersOauth($params){
        $responseReturn = $this->ndeService->callNde('AccountDeleteSecurityAnswersOauth', 'delete', $params);
        return $responseReturn;
    }

}
