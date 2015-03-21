<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::permanentRedirect('', 'login');

Route::group(['namespace' => 'App\Http\Controllers\Auth'], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'LoginController@loginScreen']);
    Route::post('doLogin', ['as' => 'doLogin', 'uses' => 'LoginController@login']);
    Route::get('resetpassword', ['as' => 'resetpassword', 'uses' => 'LoginController@resetPasswordScreen']);
    Route::get('resetsecurity', ['as' => 'resetsecurity', 'uses' => 'LoginController@resetSecurityScreen']);
    Route::get('changepassword', ['as' => 'changepassword', 'uses' => 'LoginController@changePasswordScreen']);
    Route::get('getAccessToken', ['as' => 'getAccessToken', 'uses' => 'LoginController@getAccessToken']);
    Route::get('getAccessTokenRefresh', ['as' => 'getAccessTokenRefresh', 'uses' => 'LoginController@getAccessTokenRefresh']);

    Route::get('getcaptchaform', 'LoginController@getCaptchaForm');
    Route::get('captcha', ['as' => 'captcha', 'uses' => 'LoginController@captchaScreen']);
    Route::post('doverifycaptcha', ['as' => 'doverifycaptcha', 'uses' => 'LoginController@doVerifyCaptcha']);

    Route::get('mfa-mobile', ['as' => 'mfa-mobile', 'uses' => 'LoginController@mfaMobile']);
    Route::get('mfa-email', ['as' => 'mfa-email', 'uses' => 'LoginController@mfaEmail']);
    Route::get('mfa-both', ['as' => 'mfa-both', 'uses' => 'LoginController@mfaBoth']);
    Route::post('getMfaOauth', ['as' => 'getMfaOauth', 'uses' => 'LoginController@getMfaOauth']);
    Route::post('accountGetPasswordResetQuestionsOauth', ['as' => 'accountGetPasswordResetQuestionsOauth', 'uses' => 'LoginController@accountGetPasswordResetQuestionsOauth']);
    Route::post('oauthMfa', ['as' => 'oauthMfa', 'uses' => 'LoginController@oauthMfa']);
    Route::post('accountConfirmSecurityAnswersOauth', ['as' => 'accountConfirmSecurityAnswersOauth', 'uses' => 'LoginController@accountConfirmSecurityAnswersOauth']);
    Route::post('endOauthSession', ['as' => 'endOauthSession', 'uses' => 'LoginController@endOauthSession']);
    Route::post('getSystemSettingOauth', ['as' => 'getSystemSettingOauth', 'uses' => 'LoginController@getSystemSettingOauth']);
    Route::post('resendMfaEmailOauth', ['as' => 'resendMfaEmailOauth', 'uses' => 'LoginController@resendMfaEmailOauth']);
});

Route::group(['namespace' => 'App\Http\Controllers\User'], function () {
    Route::post('accountSetNewPasswordOauth', ['as' => 'accountSetNewPasswordOauth', 'uses' => 'UserController@accountSetNewPasswordOauth']);
    Route::post('accountReplaceSecurityAnswersOauth', ['as' => 'accountReplaceSecurityAnswersOauth', 'uses' => 'UserController@accountReplaceSecurityAnswersOauth']);
    Route::post('getAvatarOauth', ['as' => 'getAvatarOauth', 'uses' => 'UserController@getAvatarOauth']);
    Route::post('getUserIdOauth', ['as' => 'getUserIdOauth', 'uses' => 'UserController@getUserIdOauth']);
    Route::post('uploadAvatarOauth', ['as' => 'uploadAvatarOauth', 'uses' => 'UserController@uploadAvatarOauth']);
    Route::post('getProfileAvatarInfo', ['as' => 'getProfileAvatarInfo', 'uses' => 'UserController@getProfileAvatarInfo']);
    Route::post('downloadAvatarForce', ['as' => 'downloadAvatarForce', 'uses' => 'UserController@downloadAvatarForce']);
});

Route::group(['namespace' => 'App\Http\Controllers\Dashboard'], function () {
    Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@dashboard']);
    Route::get('search-profile', ['as' => 'search-profile', 'uses' => 'DashboardController@searchProfile']);
    Route::get('setQuestions', ['as' => 'setQuestions', 'uses' => 'DashboardController@setQuestions']);
    Route::get('viewrecord', ['as' => 'viewrecord', 'uses' => 'DashboardController@renderViewRecord']);
    Route::get('testviewforapis', ['as' => 'testviewforapis', 'uses' => 'DashboardController@testviewforapis']);

    // Ajax
    Route::post('getControls', ['as' => 'getControls', 'uses' => 'DashboardController@getControls']);
    Route::post('getPossibleSearchData', ['as' => 'getPossibleSearchData', 'uses' => 'DashboardController@getPossibleSearchData']);
    Route::post('getSearchImages', ['as' => 'getSearchImages', 'uses' => 'DashboardController@getSearchImages']);
    Route::post('getDocHistoryOauth', ['as' => 'getDocHistoryOauth', 'uses' => 'DashboardController@getDocHistoryOauth']);
    Route::post('checkDatafeedCompletionOauth', ['as' => 'checkDatafeedCompletionOauth', 'uses' => 'DashboardController@checkDatafeedCompletionOauth']);
    Route::post('getViewRecordOauth', ['as' => 'getViewRecordOauth', 'uses' => 'DashboardController@getViewRecordOauth']);
    Route::post('getPiecesByDocIdOauth', ['as' => 'getPiecesByDocIdOauth', 'uses' => 'DashboardController@getPiecesByDocIdOauth']);

    Route::post('getSearchHistoryOauth', ['as' => 'getSearchHistoryOauth', 'uses' => 'DashboardController@getSearchHistoryOauth']);
    Route::post('getWatchlistOauth', ['as' => 'getWatchlistOauth', 'uses' => 'DashboardController@getWatchlistOauth']);
    Route::post('deleteUserWatchlistOauth', ['as' => 'deleteUserWatchlistOauth', 'uses' => 'DashboardController@deleteUserWatchlistOauth']);

    Route::post('getScanModalInfo', ['as' => 'getScanModalInfo', 'uses' => 'DashboardController@getScanModalInfo']);
    Route::post('getBookmarksOauth', ['as' => 'getBookmarksOauth', 'uses' => 'DashboardController@getBookmarksOauth']);

    Route::post('customOnDemandReportOauth', ['as' => 'customOnDemandReportOauth', 'uses' => 'DashboardController@customOnDemandReportOauth']);

    Route::post('getDownloads', ['as' => 'getDownloads', 'uses' => 'DashboardController@getDownloads']);

    Route::post('getAllowDeleteIndOauth', ['as' => 'getAllowDeleteIndOauth', 'uses' => 'DashboardController@getAllowDeleteIndOauth']);
    Route::post('deleteDocumentOauth', ['as' => 'deleteDocumentOauth', 'uses' => 'DashboardController@deleteDocumentOauth']);
    Route::post('getLastActivityOauth', ['as' => 'getLastActivityOauth', 'uses' => 'DashboardController@getLastActivityOauth']);
    Route::post('deletePieceOauth', ['as' => 'deletePieceOauth', 'uses' => 'DashboardController@deletePieceOauth']);
    Route::post('addUserWatchlistOauth', ['as' => 'addUserWatchlistOauth', 'uses' => 'DashboardController@addUserWatchlistOauth']);
    Route::post('createUserWatchlistOauth', ['as' => 'createUserWatchlistOauth', 'uses' => 'DashboardController@createUserWatchlistOauth']);
    Route::post('requestDocFromStorageOauth', ['as' => 'requestDocFromStorageOauth', 'uses' => 'DashboardController@requestDocFromStorageOauth']);
    Route::post('checkProfilePageOrderOauth', ['as' => 'checkProfilePageOrderOauth', 'uses' => 'DashboardController@checkProfilePageOrderOauth']);
    Route::post('lockDocOauth', ['as' => 'lockDocOauth', 'uses' => 'DashboardController@lockDocOauth']);
    Route::post('getAllowEditIndOauth', ['as' => 'getAllowEditIndOauth', 'uses' => 'DashboardController@getAllowEditIndOauth']);
    Route::post('getAllowNewContentIndOauth', ['as' => 'getAllowNewContentIndOauth', 'uses' => 'DashboardController@getAllowNewContentIndOauth']);
    Route::post('mergeRecordOauth', ['as' => 'mergeRecordOauth', 'uses' => 'DashboardController@mergeRecordOauth']);
    Route::post('getDoubleEntryOauth', ['as' => 'getDoubleEntryOauth', 'uses' => 'DashboardController@getDoubleEntryOauth']);
    Route::post('createDocumentOauth', ['as' => 'createDocumentOauth', 'uses' => 'DashboardController@createDocumentOauth']);
    Route::post('getProfileInfoOauth', ['as' => 'getProfileInfoOauth', 'uses' => 'DashboardController@getProfileInfoOauth']);
    Route::post('getCheckFieldHint', ['as' => 'getCheckFieldHint', 'uses' => 'DashboardController@getCheckFieldHint']);
    Route::post('checkImageFieldsOauth', ['as' => 'checkImageFieldsOauth', 'uses' => 'DashboardController@checkImageFieldsOauth']);
    Route::post('getAllTabs', ['as' => 'getAllTabs', 'uses' => 'DashboardController@getAllTabs']);
    Route::post('getDocTypeOauth', ['as' => 'getDocTypeOauth', 'uses' => 'DashboardController@getDocTypeOauth']);
    Route::get('getWorkflowProfilesOauth', ['as' => 'getWorkflowProfilesOauth', 'uses' => 'DashboardController@getWorkflowProfilesOauth']);
    Route::post('queueDownloadOauth', ['as' => 'queueDownloadOauth', 'uses' => 'DashboardController@queueDownloadOauth']);
    Route::post('transferTabOauth', ['as' => 'transferTabOauth', 'uses' => 'DashboardController@transferTabOauth']);
    Route::post('unLockDocOauth', ['as' => 'unLockDocOauth', 'uses' => 'DashboardController@unLockDocOauth']);
    Route::post('editDocIndexingFieldsOauth', ['as' => 'editDocIndexingFieldsOauth', 'uses' => 'DashboardController@editDocIndexingFieldsOauth']);
    Route::post('getActionsOauth', ['as' => 'getActionsOauth', 'uses' => 'DashboardController@getActionsOauth']);
    Route::post('sortRecordOrderOauth', ['as' => 'sortRecordOrderOauth', 'uses' => 'DashboardController@sortRecordOrderOauth']);
    Route::post('updateImageStatusOauth', ['as' => 'updateImageStatusOauth', 'uses' => 'DashboardController@updateImageStatusOauth']);
    Route::post('checkEditBulkFieldsOauth', ['as' => 'checkEditBulkFieldsOauth', 'uses' => 'DashboardController@checkEditBulkFieldsOauth']);
    Route::post('bulkEditDocFields', ['as' => 'bulkEditDocFields', 'uses' => 'DashboardController@bulkEditDocFields']);
    Route::get('getModulesOauth', ['as' => 'getModulesOauth', 'uses' => 'DashboardController@getModulesOauth']); 

    Route::get('getENV', ['as' => 'getENV', 'uses' => 'DashboardController@getENV']);
});

Route::group(['namespace' => 'App\Http\Controllers\Upload'], function () {
    // Ajax
    Route::post('uploadFileMetadataOauth', ['as' => 'uploadFileMetadataOauth', 'uses' => 'UploadController@uploadFileMetadataOauth']);
    Route::post('getAnnotationTypesOauth', ['as' => 'getAnnotationTypesOauth', 'uses' => 'UploadController@getAnnotationTypesOauth']);
});

Route::group(['namespace' => 'App\Http\Controllers\Profile'], function () {
    Route::get('dashboard/support/mydocs', ['as' => 'dashboard/support/mydocs', 'uses' => 'ProfileController@renderMyDocuments']);
    Route::post('getDownloadOauth', ['as' => 'getDownloadOauth', 'uses' => 'ProfileController@getDownloadOauth']);
    Route::post('deleteDownloadOauth', ['as' => 'deleteDownloadOauth', 'uses' => 'ProfileController@deleteDownloadOauth']);
    Route::post('getSharableAccountsOauth', ['as' => 'getSharableAccountsOauth', 'uses' => 'ProfileController@getSharableAccountsOauth']);
    Route::post('shareMyDocsOauth', ['as' => 'shareMyDocsOauth', 'uses' => 'ProfileController@shareMyDocsOauth']);
    Route::post('getProfilesOauth', ['as' => 'getProfilesOauth', 'uses' => 'ProfileController@getProfilesOauth']);
});

Route::group(['namespace' => 'App\Http\Controllers\HelpCenter'], function () {
    Route::get('help-center/basics', ['as' => 'help-center/basics', 'uses' => 'HelpCenterController@renderScreenBasics']);
	Route::get('help-center/issues', ['as' => 'help-center/issues', 'uses' => 'HelpCenterController@renderScreenIssues']);
    Route::get('help-center', ['as' => 'help-center', 'uses' => 'HelpCenterController@renderScreen']);
	Route::get('faq', ['as' => 'faq', 'uses' => 'HelpCenterController@renderFAQ']);
	// APIs
	Route::post('getHelpContentsOauth', ['as' => 'getHelpContentsOauth', 'uses' => 'HelpCenterController@getHelpContentsOauth']);
  Route::post('getFAQContentsOauth', ['as' => 'getFAQContentsOauth', 'uses' => 'HelpCenterController@getFAQContentsOauth']);
});

Route::group(['namespace' => 'App\Http\Controllers\Report'], function () {
    Route::get('dashboard/report/deletion', ['as' => 'dashboard/report/deletion', 'uses' => 'ReportController@renderDeletionReport']);
    Route::get('dashboard/report/modification', ['as' => 'dashboard/report/modification', 'uses' => 'ReportController@renderModificationReport']);
    Route::get('dashboard/report/recordsMetadata', ['as' => 'dashboard/report/recordsMetadata', 'uses' => 'ReportController@renderMetadataReport']);
    Route::get('dashboard/report/userDocumentHistory', ['as' => 'dashboard/report/userDocumentHistory', 'uses' => 'ReportController@renderUserDocumentHistoryReport']);
    Route::get('dashboard/report/scannerUsage', ['as' => 'dashboard/report/scannerUsage', 'uses' => 'ReportController@renderScannerUsageReport']);
	Route::get('dashboard/report/retention', ['as' => 'dashboard/report/retention', 'uses' => 'ReportController@renderRetentionReport']);
    Route::get('dashboard/report/metadata', ['as' => 'dashboard/report/metadata', 'uses' => 'ReportController@renderMetadataReport']);
    Route::get('dashboard/report/userDocumentHistoryReport', ['as' => 'dashboard/report/userDocumentHistoryReport', 'uses' => 'ReportController@renderUserDocumentHistoryReport']);
    Route::get('dashboard/report/userProfileRights', ['as' => 'dashboard/report/userProfileRights', 'uses' => 'ReportController@renderUserProfileReport']);
    Route::get('dashboard/report/eventLog', ['as' => 'dashboard/report/eventLog', 'uses' => 'ReportController@renderEventLogReport']);
    Route::get('dashboard/report/networkScannerUsage', ['as' => 'dashboard/report/networkScannerUsage', 'uses' => 'ReportController@renderNetworkScannerUsageReport']);
    Route::get('ondemand_report', ['as' => 'ondemand_report', 'uses' => 'ReportController@renderOndemand']);
    // APIs
    Route::get('getReportControlsOauth', ['as' => 'getReportControlsOauth', 'uses' => 'ReportController@getReportControlsOauth']);
    Route::post('getModificationReportOauth', ['as' => 'getModificationReportOauth', 'uses' => 'ReportController@getModificationReportOauth']);
    Route::get('dashboard/report/loginTracking', ['as' => 'dashboard/report/loginTracking', 'uses' => 'ReportController@renderLoginTrackingReport']);
    Route::post('loginTrackingReportOauth', ['as' => 'loginTrackingReportOauth', 'uses' => 'ReportController@loginTrackingReportOauth']);
    Route::post('userDocumentHistoryReportOauth', ['as' => 'userDocumentHistoryReportOauth', 'uses' => 'ReportController@userDocumentHistoryReportOauth']);
    Route::post('recordMetadataReportOauth', ['as' => 'recordMetadataReportOauth', 'uses' => 'ReportController@recordMetadataReportOauth']);
    Route::post('scannerUsageReportOauth', ['as' => 'scannerUsageReportOauth', 'uses' => 'ReportController@scannerUsageReportOauth']);
    Route::post('deletionReportOauth', ['as' => 'deletionReportOauth', 'uses' => 'ReportController@deletionReportOauth']);
	Route::post('retentionReportOauth', ['as' => 'retentionReportOauth', 'uses' => 'ReportController@retentionReportOauth']);
    Route::post('userProfileReportOauth', ['as' => 'userProfileReportOauth', 'uses' => 'ReportController@userProfileReportOauth']);
    Route::post('eventLogReportOauth', ['as' => 'eventLogReportOauth', 'uses' => 'ReportController@eventLogReportOauth']);
    Route::post('networkScannerUsageReportOauth', ['as' => 'networkScannerUsageReportOauth', 'uses' => 'ReportController@networkScannerUsageReportOauth']);
    Route::post('exportToCSV', ['as' => 'exportToCSV', 'uses' => 'ReportController@exportToCSV']);
    Route::post('getCustomReportControlsOauth', ['as' => 'getCustomReportControlsOauth', 'uses' => 'ReportController@getCustomReportControlsOauth']);
});

Route::group(['namespace' => 'App\Http\Controllers\Setting'], function () {
    Route::get('setting/defaultSetting', ['as' => 'setting/defaultSetting', 'uses' => 'SettingController@renderScreenDefaultSetting']);
	Route::get('setting/changePassword', ['as' => 'setting/changePassword', 'uses' => 'SettingController@renderScreenChangePassword']);
    Route::get('setting/edityourprofile', ['as' => 'setting/edityourprofile', 'uses' => 'SettingController@renderEditYourProfile']);
	Route::get('setting/hideShowColumn', ['as' => 'setting/hideShowColumn', 'uses' => 'SettingController@renderScreenHideShowColumn']);
    // APIs
    Route::post('getHideShowColumn', ['as' => 'getHideShowColumn', 'uses' => 'SettingController@getHideShowColumn']);
    Route::post('saveHideShowColumn', ['as' => 'saveHideShowColumn', 'uses' => 'SettingController@saveHideShowColumn']);
    Route::post('saveDefaultSettingsOauth', ['as' => 'saveDefaultSettingsOauth', 'uses' => 'SettingController@saveDefaultSettingsOauth']);

});

Route::group(['namespace' => 'App\Http\Controllers\Setting'], function () {
    Route::get('setting/defaultSetting', ['as' => 'setting/defaultSetting', 'uses' => 'SettingController@renderScreenDefaultSetting']);
	Route::get('setting/changePassword', ['as' => 'setting/changePassword', 'uses' => 'SettingController@renderScreenChangePassword']);

    // APIs
    Route::post('accountUpdateOldPasswordOauth', ['as' => 'accountUpdateOldPasswordOauth', 'uses' => 'SettingController@accountUpdateOldPasswordOauth']);

});

Route::group(['namespace' => 'App\Http\Controllers\Administration'], function () {
    // Route::get('administration/hideShowColumn', ['as' => 'administration/hideShowColumn', 'uses' => 'AdministrationController@renderScreenHideShowColumn']);
    Route::get('administration/manageUsers', ['as' => 'administration/manageUsers', 'uses' => 'AdministrationController@renderManageUsers']);
    Route::get('administration/general', ['as' => 'administration/general', 'uses' => 'AdministrationController@renderGeneral']);

    Route::get('administration/predefined-tab', ['as' => 'administration/predefined-tab', 'uses' => 'AdministrationController@predefinedTab']);
    // Route::get('administration/emaiIntake', ['as' => 'administration/emaiItake', 'uses' => 'AdministrationController@renderEmailIntake']);
    Route::get('administration/oneCaseControl', ['as' => 'administration/oneCaseControl', 'uses' => 'AdministrationController@renderOneCaseControl']);
    Route::get('administration/generalTab', ['as' => 'administration/generalTab', 'uses' => 'AdministrationController@generalTab']);
    // APIs
    Route::post('getProvinces', ['as' => 'getProvinces', 'uses' => 'AdministrationController@getProvinces']);
    Route::post('getHideShowColumn', ['as' => 'getHideShowColumn', 'uses' => 'AdministrationController@getHideShowColumn']);
    Route::post('saveHideShowColumn', ['as' => 'saveHideShowColumn', 'uses' => 'AdministrationController@saveHideShowColumn']);
    Route::post('getPredefinedTabsOauth', ['as' => 'getPredefinedTabsOauth', 'uses' => 'AdministrationController@getPredefinedTabsOauth']);
    Route::post('savePredefinedTabsOauth', ['as' => 'savePredefinedTabsOauth', 'uses' => 'AdministrationController@savePredefinedTabsOauth']);
    Route::post('getEmailIntakeSettingsOauth', ['as' => 'getEmailIntakeSettingsOauth', 'uses' => 'AdministrationController@getEmailIntakeSettingsOauth']);
    Route::post('saveEmailIntakeOauth', ['as' => 'saveEmailIntakeOauth', 'uses' => 'AdministrationController@saveEmailIntakeOauth']);
	Route::delete('accountDeleteSecurityAnswerOauth', ['as' => 'accountDeleteSecurityAnswerOauth', 'uses' => 'AdministrationController@accountDeleteSecurityAnswerOauth']);
	Route::delete('resetAccountQuestionsOauth', ['as' => 'resetAccountQuestionsOauth', 'uses' => 'AdministrationController@resetAccountQuestionsOauth']);
    Route::post('accountForcePasswordResetOauth', ['as' => 'accountForcePasswordResetOauth', 'uses' => 'AdministrationController@accountForcePasswordResetOauth']);
    Route::post('getOneCaseTrainingOauth', ['as' => 'getOneCaseTrainingOauth', 'uses' => 'AdministrationController@getOneCaseTrainingOauth']);
    Route::post('saveOneCaseTrainingOauth', ['as' => 'saveOneCaseTrainingOauth', 'uses' => 'AdministrationController@saveOneCaseTrainingOauth']);
    Route::post('resetUserPassOauth', ['as' => 'resetUserPassOauth', 'uses' => 'AdministrationController@resetUserPassOauth']);
    Route::post('editUserInfoOauth', ['as' => 'editUserInfoOauth', 'uses' => 'AdministrationController@editUserInfoOauth']);
    Route::post('createNewUserOauth', ['as' => 'createNewUserOauth', 'uses' => 'AdministrationController@createNewUserOauth']);
	Route::post('deleteUserInfoOauth', ['as' => 'deleteUserInfoOauth', 'uses' => 'AdministrationController@deleteUserInfoOauth']);
    Route::get('adminGetUsersOauth', ['as' => 'adminGetUsersOauth', 'uses' => 'AdministrationController@adminGetUsersOauth']);
    Route::get('adminGetUserDetailsOauth', ['as' => 'adminGetUserDetailsOauth', 'uses' => 'AdministrationController@adminGetUserDetailsOauth']);
    Route::get('getPermissionControlsOauth', ['as' => 'getPermissionControlsOauth', 'uses' => 'AdministrationController@getPermissionControlsOauth']);
    Route::post('insertIntoUserAuditOauth', ['as' => 'insertIntoUserAuditOauth', 'uses' => 'AdministrationController@insertIntoUserAuditOauth']);
	Route::post('createNewProvinceOauth', ['as' => 'createNewProvinceOauth', 'uses' => 'AdministrationController@createNewProvinceOauth']);
	Route::post('editProvinceInfoOauth', ['as' => 'editProvinceInfoOauth', 'uses' => 'AdministrationController@editProvinceInfoOauth']);
	Route::post('deleteProvinceInfoOauth', ['as' => 'deleteProvinceInfoOauth', 'uses' => 'AdministrationController@deleteProvinceInfoOauth']);
	Route::post('getMfaQROauth', ['as' => 'getMfaQROauth', 'uses' => 'AdministrationController@getMfaQROauth']);
	Route::post('addDatafeedEntriesOauth', ['as' => 'addDatafeedEntriesOauth', 'uses' => 'AdministrationController@addDatafeedEntriesOauth']);
	Route::post('saveTempFileOauth', ['as' => 'saveTempFileOauth', 'uses' => 'AdministrationController@saveTempFileOauth']);
    Route::delete('accountDeleteSecurityAnswersOauth', ['as' => 'accountDeleteSecurityAnswersOauth', 'uses' => 'AdministrationController@accountDeleteSecurityAnswersOauth']);
});

Route::group(['namespace' => 'App\Http\Controllers\Doc'], function () {
    // APIs
    Route::post('getDocMimeTypeOauth', ['as' => 'getDocMimeTypeOauth', 'uses' => 'DocController@getDocMimeTypeOauth']);
});

Route::group(['namespace' => 'App\Http\Controllers\TabActions'], function () {
    // APIs
    Route::post('addTabOauth', ['as' => 'addTabOauth', 'uses' => 'TabActionsController@addTabOauth']);
    Route::post('renameTabOauth', ['as' => 'renameTabOauth', 'uses' => 'TabActionsController@renameTabOauth']);
    Route::post('copyToMyDocumentsOauth', ['as' => 'copyToMyDocumentsOauth', 'uses' => 'TabActionsController@copyToMyDocumentsOauth']);
    Route::post('deleteTabOauth', ['as' => 'deleteTabOauth', 'uses' => 'TabActionsController@deleteTabOauth']);
    Route::post('changeTabNameOauth', ['as' => 'ChangeTabNameOauth', 'uses' => 'TabActionsController@changeTabNameOauth']);
    Route::post('deletePredefinedTabOauth', ['as' => 'deletePredefinedTabOauth', 'uses' => 'TabActionsController@deletePredefinedTabOauth']);

});

Route::group(['namespace' => 'App\Http\Controllers\Scan'], function () {
    // APIs
    Route::post('viewScanOptionsOauth', ['as' => 'viewScanOptionsOauth', 'uses' => 'ScanController@viewScanOptionsOauth']);
    Route::post('getPredefinedBookmarkOauth', ['as' => 'getPredefinedBookmarkOauth', 'uses' => 'ScanController@getPredefinedBookmarkOauth']);
});

Route::group(['namespace' => 'App\Http\Controllers\ViewDocument'], function () {
  // APIs
    Route::post('getTabFilesOauth', ['as' => 'getTabFilesOauth', 'uses' => 'ViewDocumentController@getTabFilesOauth']);
    Route::post('getTabFilesHistoryOauth', ['as' => 'getTabFilesHistoryOauth', 'uses' => 'ViewDocumentController@getTabFilesHistoryOauth']);
    Route::post('updateDocumentPieceOauth', ['as' => 'updateDocumentPieceOauth', 'uses' => 'ViewDocumentController@updateDocumentPieceOauth']);
    Route::post('getLinksOauth', ['as' => 'getLinksOauth', 'uses' => 'ViewDocumentController@getLinksOauth']);
    Route::post('cancelLinkOauth', ['as' => 'cancelLinkOauth', 'uses' => 'ViewDocumentController@cancelLinkOauth']);
    Route::post('addTempLinkOauth', ['as' => 'addTempLinkOauth', 'uses' => 'ViewDocumentController@addTempLinkOauth']);
    Route::post('getFileLockStatusOauth', ['as' => 'getFileLockStatusOauth', 'uses' => 'ViewDocumentController@getFileLockStatusOauth']);
    Route::post('lockFileOauth', ['as' => 'lockFileOauth', 'uses' => 'ViewDocumentController@lockFileOauth']);
    Route::post('getFolderPredefinedOauth', ['as' => 'getFolderPredefinedOauth', 'uses' => 'ViewDocumentController@getFolderPredefinedOauth']);
    Route::post('addFolderOauth', ['as' => 'addFolderOauth', 'uses' => 'ViewDocumentController@addFolderOauth']);
    Route::post('deleteFolderOauth', ['as' => 'deleteFolderOauth', 'uses' => 'ViewDocumentController@deleteFolderOauth']);
    Route::post('renameFolderOauth', ['as' => 'renameFolderOauth', 'uses' => 'ViewDocumentController@renameFolderOauth']);
    Route::post('updatePieceFilenameOauth', ['as' => 'updatePieceFilenameOauth', 'uses' => 'ViewDocumentController@updatePieceFilenameOauth']);
});


Route::group(['namespace' => 'App\Http\Controllers\Notification'], function () {
    Route::get('dashboard/notification', ['as' => 'dashboard/notification', 'uses' => 'NotificationController@renderNotification']);
    // API
    Route::get('getAllNotificationsOauth', ['as' => 'getAllNotificationsOauth', 'uses' => 'NotificationController@getAllNotificationsOauth']);
    Route::get('getUnreadNotificationOauth', ['as' => 'getUnreadNotificationOauth', 'uses' => 'NotificationController@getUnreadNotificationOauth']);
    Route::post('readNotificationOauth', ['as' => 'readNotificationOauth', 'uses' => 'NotificationController@readNotificationOauth']);
});

Route::group(['namespace' => 'App\Http\Controllers\Province'], function () {
    // API
    Route::post('getCollectedProvincePermissions', ['as' => 'getCollectedProvincePermissions', 'uses' => 'ProvinceController@getCollectedProvincePermissions']);
});
