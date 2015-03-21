<?php
namespace App\Http\Controllers\Administration;
use Exception;
use App\Http\Controllers\Controller;
use App\Services\AdministrationService;
use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use function view;

class AdministrationController extends Controller {
  private $administrationService;
  private $dashboardService;

  public function __construct(DashboardService $dashboardService, AdministrationService $administrationService){
    $this->administrationService = $administrationService;
	$this->dashboardService = $dashboardService;
  }

  public function renderGeneral()
  {
	$responseReturn = $this->dashboardService->getProfiles();
	if ($responseReturn['status'] === 'success') {
		$responseBody = $responseReturn['body'];
		{
			return Inertia::render('administration/General', [
			 'programsJson' => json_encode($responseBody->data->profiles)]);
		}
	}
	else {
		return redirect('/login');
	}
  }

  public function renderManageUsers()
  {
	$responseReturn = $this->dashboardService->getProfiles();
	if ($responseReturn['status'] === 'success') {
		$responseBody = $responseReturn['body'];
		{
			return Inertia::render('administration/ManageUsers', [
			 'programsJson' => json_encode($responseBody->data->profiles)]);
		}
	}
	else {
		return redirect('/login');
	}
  }

  public function getProvinces(Request $request) {
    try {
        $params = [
            'page' => $request->input('page'),
            'page_size' => $request->input('page_size')
        ];
        $responseReturn = $this->administrationService->getProvinces($params);

        if ($responseReturn['status'] === 'success') {
            $responseBody = $responseReturn['body'];
            return $this->apiSuccess('success', ['data' => $responseBody->data]);
        } else {
            return $this->apiError('error', ['message' => $responseReturn]);
        }
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

    public function generalTab()
    {
        $responseReturn = $this->dashboardService->getProfiles();
        if ($responseReturn['status'] === 'success') {
            $responseBody = $responseReturn['body'];
            {
                return Inertia::render('administration/GeneralTab', [
                'programsJson' => json_encode($responseBody->data->profiles),]);
            }
        }
        else {
            return redirect('/login');
        }
    }

    public function predefinedTab() {
        $responseReturn = $this->dashboardService->getProfiles();
        if ($responseReturn['status'] === 'success') {
            $responseBody = $responseReturn['body'];
            {
                return Inertia::render('administration/PredefinedTab', [
                'programsJson' => json_encode($responseBody->data->profiles),]);
            }
        }
        else {
            return redirect('/login');
        }
    }

    // public function renderEmailIntake()
    // {
    //     $responseReturn = $this->dashboardService->getProfiles();
    //     if ($responseReturn['status'] === 'success') {
    //         $responseBody = $responseReturn['body'];
    //         {
    //             return Inertia::render('administration/EmailIntake', [
    //             'programsJson' => json_encode($responseBody->data->profiles),]);
    //         }
    //     }
    //     else {
    //         return redirect('/login');
    //     }
    // }

    public function getHideShowColumn(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id')
            ];
            $response = $this->administrationService->getHideShowColumn($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['hideShowCols' => $response['body']->data->hideShowCols]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function saveHideShowColumn(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'columns' => $request->input('columns')
            ];

            $response = $this->administrationService->saveHideShowColumn($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']->message]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getPredefinedTabsOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'defaultTab' => 0
            ];

            $response = $this->administrationService->getPredefinedTabsOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function savePredefinedTabsOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'tabs' => $request->input('tabs')
            ];

            $response = $this->administrationService->savePredefinedTabsOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getEmailIntakeSettingsOauth() {
        try {
            $params = [[]];

            $response = $this->administrationService->getEmailIntakeSettingsOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function saveEmailIntakeOauth(Request $request) {
        try {
            $params = [
                'emailSetting' => $request->input('emailSetting'),
                'format' => $request->input('format')
            ];

            $response = $this->administrationService->saveEmailIntakeOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function saveTempFileOauth(Request $request) {
        try {
            $params = [
                'profile_id' => $request->input('profile_id'),
                'file' => $request->input('file')
            ];

            $response = $this->administrationService->saveTempFileOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function renderOneCaseControl()
    {
        $responseReturn = $this->dashboardService->getProfiles();
        if ($responseReturn['status'] === 'success') {
            $responseBody = $responseReturn['body'];
            {
                return Inertia::render('administration/OneCaseControl', [
                'programsJson' => json_encode($responseBody->data->profiles),]);
            }
        }
        else {
            return redirect('/login');
        }
    }

    public function getOneCaseTrainingOauth() {
        try {
            $params = [[]];

            $response = $this->administrationService->getOneCaseTrainingOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function saveOneCaseTrainingOauth(Request $request) {
        try {
            $params = [
                'oc_training' => $request->input('oc_training'),
            ];

            $response = $this->administrationService->saveOneCaseTrainingOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function accountForcePasswordResetOauth(Request $request) {
        try {

            $params = [
                'user_id' => $request->input('userID'),
            ];

            $response = $this->administrationService->accountForcePasswordResetOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function addDatafeedEntriesOauth(Request $request) {
        try {
            // $columns = $request->input('columns');
            // $fields = [];
            // foreach ($columns as $item) {
            //     array_push($fields, [$item]);
            // }
            $params = [
                'columns' => $request->input('columns'),
                'access_key' => $request->input('access_key'),
                'csv_file' => $request->input('csv_file'),
                'has_header' => $request->input('has_header'),
                'profile_id' => $request->input('profile_id'),
            ];

            $response = $this->administrationService->addDatafeedEntriesOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function editUserInfoOauth(Request $request) {
        try {
            $fields = $request->input('user_info');
            $fieldsParam = [];

            foreach ($fields as $key=>$value) {
                $fieldsParam[$key] = $value;
            }

            $params = [
                'user_info' => $fieldsParam
            ];

            $response = $this->administrationService->editUserInfoOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function createNewUserOauth(Request $request) {
        try {
            $fields = $request->input('user_info');
            $fieldsParam = [];

            foreach ($fields as $key=>$value) {
                $fieldsParam[$key] = $value;
            }

            $params = [
                'user_info' => $fieldsParam
            ];

            $response = $this->administrationService->createNewUserOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function deleteUserInfoOauth(Request $request) {
        try {
            $params = [
				'user_id' => $request->input('user_id'),
            ];
            $response = $this->administrationService->deleteUserInfoOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function adminGetUsersOauth(Request $request) {
        try {
            $params = [
                'password_status' => $request->input('password_status'),
                'is_locked' => $request->input('is_locked'),
                'in_province' => $request->input('in_province'),
                'name_search' => $request->input('name_search'),
                'page_size' => $request->input('page_size'),
                'page' => $request->input('page'),
                'order_by' => $request->input('order_by'),
            ];

            $response = $this->administrationService->adminGetUsersOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['message' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function adminGetUserDetailsOauth(Request $request) {
        try {
            $params = [
                'user_id' => $request->input('user_id'),
            ];

            $response = $this->administrationService->adminGetUserDetailsOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getPermissionControlsOauth() {
        try {
            $response = $this->administrationService->getPermissionControlsOauth();
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function insertIntoUserAuditOauth(Request $request) {
        try {
            $params = [
                'level' => $request->input('level'),
                'message' => $request->input('message'),
            ];

            $response = $this->administrationService->insertIntoUserAuditOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function accountDeleteSecurityAnswersOauth(Request $request) {
        try {
            $params = [
                'reset_account_id' => $request->input('reset_account_id'),
            ];

            $response = $this->administrationService->accountDeleteSecurityAnswersOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function createNewProvinceOauth(Request $request) {
        try {
            $params = [
                'province_info' => $request->input('provinceInfo'),
            ];
            $response = $this->administrationService->createNewProvinceOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function editProvinceInfoOauth(Request $request) {
        try {
            $params = [
                'province_info' => $request->input('provinceInfo'),
				'province_id' => $request->input('provinceId'),
            ];
            $response = $this->administrationService->editProvinceInfoOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function deleteProvinceInfoOauth(Request $request) {
        try {
            $params = [
				'province_id' => $request->input('province_id'),
            ];
            $response = $this->administrationService->deleteProvinceInfoOauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

	public function getMfaQROauth(Request $request) {
        try {
            $params = [
                'user_id' => $request->input('user_id'),
            ];

            $response = $this->administrationService->getMfaQROauth($params);
            if ($response['status'] === 'success'){
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['data' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }
}
