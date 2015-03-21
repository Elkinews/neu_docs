<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\LoginService;
use App\Services\ViewDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use function view;

class DashboardController extends Controller
{

    private $dashboardService;
    private $loginService;
	private $viewDocumentService;
    private $programListAll;


    public function __construct(DashboardService $dashboardService, LoginService $loginService, ViewDocumentService $viewDocumentService){
        $this->dashboardService = $dashboardService;
        $this->loginService = $loginService;
		$this->viewDocumentService = $viewDocumentService;
        $this->programListAll = array();
    }

    public function getENV() {
        try {
            $env = [
                'MEDIA_URL' => config('nde.MEDIA_URL'),
                'FS_URL' => config('nde.FS_URL'),
                'NDE_URL' => config('nde.nde_url'),
                'DOCUVIEW_URL' => config('nde.DOCUVIEW_URL'),
                'STARRS_URL' => config('nde.STARRS_URL'),
                'GOOGLE_RECAPTCHA_KEY' => config('nde.GOOGLE_RECAPTCHA_KEY')
            ];
            return $this->apiSuccess('success', ['env' => $env]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function checkSetQuestions() {
        $response = $this->loginService->getSecurityQuestionsStatusOauth();

        if($response['status'] == 'fail') {
            return redirect('/login');
        }
        return $response['body']->data->has_questions;
    }

    public function setQuestions() {
        $response = $this->dashboardService->getAllQuestionsOauth();

        if($response['status'] == 'fail') {
            return redirect('/login');
        } else {
            return Inertia::render('dashboard/setQuestions', [
                'questions' => $response['body']->data->questions
            ]);
        }
    }

    public function dashboard()
    {
        if (!$this->checkSetQuestions()) {
            return redirect('/setQuestions');
        }

        $result_is_change_pwd = $this->loginService->getPasswordChangeRequiredOauth();
        if ($result_is_change_pwd['status'] == 'success' && $result_is_change_pwd['body']->status == 'success') {
            if ($result_is_change_pwd['body']->data->change_password) {
                return redirect('/changepassword');
            }
        } else {
            return redirect('/login');
        }

        $responseReturn = $this->dashboardService->getProfiles();

        if ($responseReturn['status'] === 'success') {
            $responseBody = $responseReturn['body'];

            $response = $this->dashboardService->getLoginUserDetailsOauth();
            if ($response['status'] === 'success') {
                $roles = $response['body']->data;
            } else {
                $roles = null;
            }
			$profileId = $responseBody->data->profiles[0]->id;

            $this->programList($responseBody->data->profiles);

			$responseBulkActions = $this->dashboardService->getBulkActions([
				'profile_id' => $profileId
			]);
            return Inertia::render('dashboard/index', [
                'profileId' => $roles->settings->defaultprofile_id ? $roles->settings->defaultprofile_id : ($this->programListAll ? $this->programListAll[0] : "1"),
                'testProgram' => $this->programListAll,
                'programsJson' => json_encode($responseBody->data->profiles),
				'bulkActions' => json_encode($responseBulkActions['body']->data),
                'pageTitle' => 'NDE Dashboard',
                'roles' => $roles,
            ]);
        }
        else{
            return redirect('/login');
        }
    }

    public function programList($programs){
        foreach ($programs as $key => $value) {
            if ($value->meta->has_key_search){
                array_push($this->programListAll, $value->id);
            }
            if ($value->meta->has_key_search && count($value->subProfiles) > 0){
                $this->programList($value->subProfiles);
            }
            if (!$value->meta->has_key_search && count($value->subProfiles) > 0){
                $this->programList($value->subProfiles);
            }
        }
    }

    public function searchProfile() {
        $profileId = $_GET['profileId'];

        if (!$this->checkSetQuestions()) {
            return redirect('/setQuestions');
        }

        $result_is_change_pwd = $this->loginService->getPasswordChangeRequiredOauth();
        if ($result_is_change_pwd['status'] == 'success' && $result_is_change_pwd['body']->status == 'success') {
            if ($result_is_change_pwd['body']->data->change_password) {
                return redirect('/changepassword');
            }
        } else {
            return redirect('/login');
        }

        $responseReturn = $this->dashboardService->getProfiles();

        if ($responseReturn['status'] === 'success') {
            $responseBody = $responseReturn['body'];

            $response = $this->dashboardService->getLoginUserDetailsOauth();
            if ($response['status'] === 'success') {
                $roles = $response['body']->data;
            } else {
                $roles = null;
            }
			// $profileId = $responseBody->data->profiles[0]->id;
            $profileIds = explode(',', $profileId);

			$responseBulkActions = $this->dashboardService->getBulkActions([
				'profile_id' => end($profileIds)
			]);
            return Inertia::render('dashboard/index', [
                'profileId' => $profileId,
                'programsJson' => json_encode($responseBody->data->profiles),
				'bulkActions' => json_encode($responseBulkActions['body']->data),
                'pageTitle' => 'NDE Dashboard',
                'roles' => $roles,
            ]);
        }
        else{
            return redirect('/login');
        }
    }

    public function renderViewRecord(Request $request) {
        try {
            $responseReturn = $this->dashboardService->getProfiles();
            if ($responseReturn['status'] === 'success') {
                $responseBody = $responseReturn['body'];
                $response = $this->dashboardService->getLoginUserDetailsOauth();
                if ($response['status'] === 'success') {
                    $roles = $response['body']->data;
                } else {
                    $roles = null;
                }

                $profileId = $_GET['profile_id'];
                
                $doc_id = $_GET['doc_id'];
                try{
                    $tab_name = $_GET['tab_name'];
                } catch(Exception $e) {
                    $tab_name =  '';
                }

                
                try{
                    $standalone = $_GET['standalone'] == 'true' ? true : false;
                } catch(Exception $e) {
                    $standalone =  false;
                }


                if(!$profileId || !$doc_id) {
                    return redirect('/dashboard');
                }

                $responseBulkActions = $this->dashboardService->getBulkActions([
                    'profile_id' => $profileId
                ]);

                return Inertia::render('dashboard/viewrecord', [
                    'programsJson' => json_encode($responseBody->data->profiles),
                    'roles' => $roles,
                    'bulkActions' => json_encode($responseBulkActions['body']->data),
                    'profileId' => $profileId,
                    'doc_id' => $doc_id,
                    'tab_name' => $tab_name,
                    'standalone' => $standalone
                ]);
            } else {
                return redirect('/login');
            }
        } catch(Exception $e) {
            return redirect('/login');
        }


    }

    public function testviewforapis() {
        return Inertia::render('apitest');
    }

    public function getControls(Request $request) {
        try {
            $param = [
                'profile_id' => $request->input('profile_id'),
				'is_bulk_indexing' => $request->input('is_bulk_indexing'),
            ];
            $responseReturn = $this->dashboardService->getControls($param);
            if ($responseReturn['status'] === 'success') {
                $responseBody = $responseReturn['body'];
                return $this->apiSuccess('success', ['controls' => $responseBody->data->controls]);
            } else {
                // $responseBody = $responseReturn['body'];
                return $this->apiError('error', ['message' => $responseReturn]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getPossibleSearchData(Request $request) {
        try {
            $fields = $request->input('fields');
            $fieldsParam = [];
            foreach ($fields as $item) {
                // array_push($fieldsParam, [$item['key'] => $item['value']]);
                $fieldsParam[$item['key']] = $item['value'];
            }
            $param = [
                'profile_id' => $request->input('profile_id'),
                'search_key' => $request->input('search_key'),
                'search_value' => $request->input('search_value'),
                'fields' => $fieldsParam
            ];

            $responseReturn = $this->dashboardService->getPossibleSearchData($param);
            if ($responseReturn['status'] === 'success') {
                $responseBody = $responseReturn['body'];
                return $this->apiSuccess('success', ['possible_values' => $responseBody->data->possible_values]);
            }
            else{
                return $this->apiError('error', ['error' => true]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getSearchImages(Request $request) {
        try {
            $Searchitems = $request->input('Searchitems');
            $fieldsParam = [];
            foreach ($Searchitems['item'] as $item) {
                array_push($fieldsParam, ['field_name' => $item['key'], 'field_value' => $item['value']]);
            }

            $exclude_fields = array();
            if ($request->input('excludeName')) {
                $exclude_fields = array(
                    'field_name' => $request->input('excludeName'),
                    'field_value' => $request->input('excludeValue')
                );
            }

            $include_fields = array();
            if ($request->input('includeName')) {
                $include_fields = array(
                    'field_name' => $request->input('includeName'),
                    'field_value' => ''
                );
            }

            $params = [
                'profile_id' => $request->input('profile'),
                'search_fields' => $fieldsParam,
                'page' => $request->input('page'),
                'page_size' => $request->input('pageSize'),
                'strict' => $request->input('strict'),
                'record_from_date' =>$request->input('recordFromDate'),
                'record_to_date' =>$request->input('recordToDate'),
				'output_csv' => $request->input('outputCSV'),
            ];

            if (!empty($include_fields)) {
              $params['include_fields'] = $include_fields;
            }

            if (!empty($exclude_fields)) {
              $params['exclude_fields'] = $exclude_fields;
            }

            if ($request->input('order_by')) {
                $params['order_by'] = $request->input('order_by');
                $params['order'] = $request->input('order');
            }

            $response = $this->dashboardService->searchImages($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data, 'p' => $params]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }


        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }

    }

    public function getScanModalInfo(Request $request) {
        try{
            $params = [
                'profile_id' => $request->input('profile_id'),
                'image_id' => $request->input('image_id')
            ];

            $responseAllowEditIndOauth = $this->dashboardService->getAllowEditIndOauth($params);
            if ($responseAllowEditIndOauth['status'] !== 'success'){
                return $this->apiError('error', ['data' => $responseAllowEditIndOauth, 'api' => 'getAllowEditIndOauth']);
            }

            return $this->apiSuccess('success', [
                'AllowEditIndOauth' => $responseAllowEditIndOauth['body']->data,
            ]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getBookmarksOauth(Request $request) {
        try{
            $data = [
                "profile" => $request->input('profile'),
                "docID" => $request->input('docID'),
                "tabName" => $request->input('tabName'),
                "boxType" => $request->input('boxType'),
                "useOrder" => 0
            ];

            $response = $this->dashboardService->getBookmarksOauth($data);
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }

    }

    public function getDocHistoryOauth(Request $request) {
        try{
            $data = [
                "profile_id" => $request->input('profile_id'),
                "order_by" => $request->input('order_by').' '. $request->input('order'),
                "page_size" => $request->input('page_size'),
				"page" => $request->input('page'),
                "from_date" => $request->input('from_date'),
                "to_date" => $request->input('to_date'),
            ];
            $response = $this->dashboardService->getDocHistoryOauth($data);
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function checkDatafeedCompletionOauth(Request $request) {
        try{
            $data = [
                "target_profile" => $request->input('target_profile'),
                "source_table" => $request->input('source_table'),
                "fields" => $request->input('fields')
            ];

            $response = $this->dashboardService->checkDatafeedCompletionOauth($data);
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function customOnDemandReportOauth(Request $request) {
        try{
            $data = [
                "profile_id" => $request->input('profile_id'),
                "order_by" => $request->input('order_by'),
                "page" => $request->input('page'),
                "page_size" => $request->input('page_size')
            ];

            $dataFilter = $request->input('dataFilter');
            $data = array_merge($data, $dataFilter);

            $response = $this->dashboardService->customOnDemandReportOauth($data);
            // return $this->apiSuccess('success', ['data' => $response]);
            if ($response['status'] == 'fail') {
                return $this->apiError('error', ['data' => $response, 'request'=> $data]);
            }

            return $this->apiSuccess('success', ['data' => $response, 'request'=> $data]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getSearchHistoryOauth(Request $request) {
        try{
            $data = [
                "profile_id" => $request->input('profile_id'),
            ];

            $response = $this->dashboardService->getSearchHistory($data);
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getWatchlistOauth(Request $request) {
        try{
            $data = [
                "profile_id" => $request->input('profileId'),
                "order_by" => $request->input('orderBy'),
                "order_dir" => $request->input('orderDir'),
            ];

            $response = $this->dashboardService->getWatchlist($data);

            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }

        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function deleteUserWatchlistOauth(Request $request) {
        try{
            $data = [
                "profileId" => $request->input('profileId'),
                "groupHash" => $request->input('groupHash'),
            ];

            $response = $this->dashboardService->deleteUserWatchlist($data);
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getDownloads(Request $request) {
        try{
            $data = [
                "profile" => $request->input('profile'),
                "offset" => $request->input('offset'),
                "limit" => $request->input('limit'),
            ];

            $response = $this->dashboardService->getDownloads($data);
            return $this->apiSuccess('success', ['data' => $response]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function getAllowDeleteIndOauth(Request $request) {
		try{
			$data = [
				"profile_id" => $request->input('profile_id'),
				"image_id" => $request->input('image_id'),
			];

			$response = $this->viewDocumentService->getGetAllowDeleteInd($data);
			return $this->apiSuccess('success', ['data' => $response['body']->data]);
		} catch(Exception $e) {
			return $this->apiError('error', ['error' => $e->getMessage()]);
		}
	}

	public function deleteDocumentOauth(Request $request) {
		try{
			$data = [
				"profile_id" => $request->input('profile_id'),
				"document_id" => $request->input('document_id'),
			];

			$response = $this->dashboardService->deleteDocument($data);

			// Validate if external API fail and return error message
			if ($response['status'] === 'fail' && $response['message']) {
				return $this->apiError('error', ['error' => $response['message']]);
			}

			return $this->apiSuccess('success', ['data' => $response['body']->data]);
		} catch(Exception $e) {
			return $this->apiError('error', ['error' => $e->getMessage()]);
		}
	}

    public function deletePieceOauth(Request $request) {
		try{
			$data = [
				"profile_id" => $request->input('profile_id'),
				"nuid" => $request->input('nuid'),
			];

			$response = $this->dashboardService->deletePieceOauth($data);
            return $this->apiSuccess('success', ['data' => $response]);
			// return $this->apiSuccess('success', ['data' => $response['body']->data]);
		} catch(Exception $e) {
			return $this->apiError('error', ['error' => $e->getMessage()]);
		}
	}

	public function getLastActivityOauth(Request $request) {
		try{
			$data = [
				"profile_id" => $request->input('profile_id'),
				"document_id" => $request->input('document_id'),
			];

			$response = $this->dashboardService->getLastActivity($data);
			return $this->apiSuccess('success', ['data' => $response['body']->data]);
		} catch(Exception $e) {
			return $this->apiError('error', ['error' => $e->getMessage()]);
		}
	}

    public function getViewRecordOauth(Request $request) {
        try{
			$data = [
				"profile_id" => $request->input('profile_id'),
				"doc_id" => $request->input('doc_id'),
			];

			$response = $this->dashboardService->getViewRecordOauth($data);
            if ($response['status'] === 'success') {
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['error' => $response]);
            }

		} catch(Exception $e) {
			return $this->apiError('error', ['error' => $e->getMessage()]);
		}
    }

    public function getPiecesByDocIdOauth(Request $request) {
        try{
			$data = [
				"profileId" => $request->input('profileId'),
				"docId" => $request->input('docId'),
			];

			$response = $this->viewDocumentService->getPiecesByDocIdOauth($data);
            if ($response['status'] === 'success') {
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['error' => $response]);
            }

		} catch(Exception $e) {
			return $this->apiError('error', ['error' => $e->getMessage()]);
		}
    }

	public function createUserWatchlistOauth(Request $request) {
        try {
            $Searchitems = $request->input('Searchitems');
            $fieldsParam = [];
            foreach ($Searchitems['item'] as $item) {
                $fieldsParam[$item['key']] = $item['value'];
            }

            $params = [
                'profile_id' => $request->input('profile_id'),
                'fields' => $fieldsParam,
                'action' => $request->input('action'),
            ];

            $response = $this->dashboardService->createUserWatchlist($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function addUserWatchlistOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'doc_id' => $request->input('doc_id'),
                'action' => $request->input('action'),
            ];
            $response = $this->dashboardService->addUserWatchlist($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function requestDocFromStorageOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'document_id' => $request->input('document_id'),
            ];
            $response = $this->dashboardService->requestDocFromStorageOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function checkProfilePageOrderOauth(Request $request) {
        try {
            $params = [
                'profile' => $request->input('profileId'),
                'targetProfileID' => $request->input('targetProfileId'),
            ];
            $response = $this->dashboardService->checkProfilePageOrderOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function lockDocOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'document_ids' => [$request->input('document_ids')],
            ];
            $response = $this->dashboardService->getLockDoc($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function getAllowEditIndOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'image_id' => $request->input('image_id'),
            ];
            $response = $this->dashboardService->getAllowEditIndOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function getAllowNewContentIndOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'image_id' => $request->input('image_id'),
            ];
            $response = $this->dashboardService->getAllowNewContentIndOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function mergeRecordOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'from_id' => $request->input('from_id'),
				'to_id' => $request->input('to_id'),
            ];
            $response = $this->dashboardService->mergeRecord($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function getDoubleEntryOauth(Request $request) {
        try {
			$Searchitems = $request->input('Searchitems');
            $fieldsParam = [];
            foreach ($Searchitems['item'] as $item) {
				$fieldsParam[$item['key']] = $item['value'];
            }
			$params = [
				'fields' => $fieldsParam,
                'profile_id' => $request->input('profile_id'),
            ];
            $response = $this->dashboardService->getDoubleEntry($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function createDocumentOauth(Request $request) {
        try {
			$Searchitems = $request->input('Searchitems');
            $fieldsParam = [];

            foreach ($Searchitems['item'] as $item) {
                $fieldsParam[$item['key']] = $item['value'];
            }
			$params = [
				'fields' => $fieldsParam,
                'profile_id' => $request->input('profileId'),
            ];

            $response = $this->dashboardService->createDocument($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['error' => $response['message'], 'errors' => $response['response']->data]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function getProfileInfoOauth(Request $request) {
        try {
            $params = [
                'profile' => $request->input('profileId'),
                'get_all' => $request->input('getAll'),
            ];
            $response = $this->dashboardService->getProfileInfoOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
			} else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }


    public function getWorkflowProfilesOauth() {
        try {
            $response = $this->dashboardService->getWorkflowProfilesOauth();
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['profiles' => $response['body']->data->profiles]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function getCheckFieldHint(Request $request) {
        try {
			$fields = $request->input('fields');
            $fieldsParam = [];
            foreach ($fields as $item) {
                $fieldsParam[$item['key']] = $item['value'];
            }
            $params = [
                'target_profile' => $request->input('targetProfileId'),
				'source_table' => $request->input('sourceTable'),
                'prefix' => $request->input('prefix'),
				'field_name' =>  $request->input('fieldName'),
                'fields' => $fieldsParam,
            ];
            $response = $this->dashboardService->getCheckFieldHint($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function checkImageFieldsOauth(Request $request) {
        try {
			$fields = $request->input('fields');
            $fieldsParam = [];
            foreach ($fields as $item) {
                $fieldsParam[$item['key']] = $item['value'];
            }
            $params = [
                'profile_id' => $request->input('profile_id'),
				'is_new_content' =>  $request->input('is_new_content'),
                'fields' => $fieldsParam,
            ];
            $response = $this->dashboardService->checkImageFieldsOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }


	public function getAllTabs(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'document_id' => $request->input('document_id'),
            ];
            $response = $this->dashboardService->getAllTabs($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function getDocTypeOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'document_id' => $request->input('document_id'),
				'oc_key' => $request->input('ocKey'),
				'fulltext' => $request->input('fulltext'),
				'workqueue' => $request->input('workqueue'),
            ];
            $response = $this->dashboardService->getDocTypeOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
			} else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function queueDownloadOauth(Request $request) {
        try {
			$image_id = $request->input('imageIDs');
            $imageIDs = [];
            foreach ($image_id as $item) {
                array_push($imageIDs, $item);
            }
			$params = [
				'imageIDs' => $imageIDs,
                'profile' => $request->input('profile'),
                'status' => $request->input('status'),
                'group' => $request->input('group'),
            ];
            $response = $this->dashboardService->queueDownloadOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function transferTabOauth(Request $request) {
        try {
			$fields = $request->input('fields');
            $fieldsParam = [];
            foreach ($fields as $item) {
                $fieldsParam[$item['key']] = $item['value'];
            }
            $params = [
				'profile_id' => $request->input('profile_id'),
				'target_profile_id' => $request->input('target_profile_id'),
				'document_id' => $request->input('document_id'),
				'target_document_id' => $request->input('target_document_id'),
				'transfer_all' => $request->input('transfer_all'),
				'fields' => $fieldsParam,
                'tabs' => $request->input('tabs'),
            ];
            $response = $this->dashboardService->transferTab($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function unLockDocOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'document_ids' => $request->input('document_ids'),
            ];
            $response = $this->dashboardService->getUnlockDoc($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function editDocIndexingFieldsOauth(Request $request) {
        try {
			$fields = $request->input('fields');
            $fieldsParam = [];
            foreach ($fields as $item) {
				if ($item['fromValue'] != $item['value']) {
					$fieldsParam[$item['key']] = [
						'from_value' => $item['fromValue'] ? $item['fromValue'] : '',
						'to_value' => $item['value']
					];
				}
            }
            $params = [
				'profile_id' => $request->input('profile_id'),
				'document_ids' => $request->input('document_ids'),
				'fields' => $fieldsParam,
            ];

            $response = $this->dashboardService->editDocIndexingFields($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function getActionsOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'document_id' => $request->input('document_id'),
            ];
            $response = $this->dashboardService->getActions($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function sortRecordOrderOauth(Request $request) {
        try {

			$documentIds = $request->input('document_ids');
			$paramDocumentIds = [];
			foreach ($documentIds as $item) {
				$paramDocumentIds[$item['documentId']] = $item['value'];
            }
			$params = [
				'profile_id' => $request->input('profile_id'),
				'document_ids' => $paramDocumentIds,
            ];
            $response = $this->dashboardService->sortRecordOrder($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function updateImageStatusOauth(Request $request) {
        try {
			$params = [
				'profile_id' => $request->input('profile_id'),
				'document_id' => $request->input('document_id'),
				'cmd' => $request->input('cmd'),
            ];
            $response = $this->dashboardService->updateImageStatusOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function checkEditBulkFieldsOauth(Request $request) {
        try {
			$fields = [];

            $fields['field_key'] = $request->input('fieldKey');
			$fields['field_value'] = $request->input('fieldValue');
            $params = [
				'profile_id' => $request->input('profile_id'),
				'document_ids' => $request->input('document_ids'),
				'fields' =>  $fields,
            ];
            $response = $this->dashboardService->checkEditBulkFieldsOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }


	public function bulkEditDocFields(Request $request) {
        try {
            $params = [
				'profile_id' => $request->input('profile_id'),
				'document_ids' => $request->input('document_ids'),
				'fields' =>  $request->input('fields'),
            ];
            $response = $this->dashboardService->bulkEditDocFields($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }
    
	public function getModulesOauth(Request $request)
    {
        try {
            $response = $this->dashboardService->getModulesOauth();
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }
}
