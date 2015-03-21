<?php

namespace App\Http\Controllers\Setting;
use Exception;
use App\Http\Controllers\Controller;
use App\Services\SettingService;
use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use function view;
class SettingController extends Controller{
  private $settingService;
  private $dashboardService;

  public function __construct(DashboardService $dashboardService, SettingService $settingService){
    $this->settingService = $settingService;
	$this->dashboardService = $dashboardService;
  }

  public function renderScreenDefaultSetting()
  {
	$responseReturn = $this->dashboardService->getProfiles();
	if ($responseReturn['status'] === 'success') {
		$responseBody = $responseReturn['body'];
		{
			Inertia::share('appName', 'hello');
			return Inertia::render('setting/DefaultSetting', [
			 'programsJson' => json_encode($responseBody->data->profiles)
			]);
		}
	}
	else {
		return redirect('/login');
	}
  }

  public function renderScreenChangePassword()
  {
	$responseReturn = $this->dashboardService->getProfiles();
	if ($responseReturn['status'] === 'success') {
		$responseBody = $responseReturn['body'];
		{
			return Inertia::render('setting/ChangePassword', [
			 'programsJson' => json_encode($responseBody->data->profiles),]);
		}
	}
	else {
		return redirect('/login');
	}
  }

  public function renderEditYourProfile()
  {
	$responseReturn = $this->dashboardService->getProfiles();
	if ($responseReturn['status'] === 'success') {
		$responseBody = $responseReturn['body'];
		{
			return Inertia::render('setting/EditYourProfile', [
			 'programsJson' => json_encode($responseBody->data->profiles),]);
		}
	}
	else {
		return redirect('/login');
	}
  }

  public function saveDefaultSettingsOauth(Request $request) {
	try{
		$data = [
			"defaultSettings" => $request->input('defaultSettings'),
		];
		$response = $this->settingService->saveDefaultSettingsOauth($data);
		if($response['status'] == 'fail') {
            return $this->apiError('error', ['data' => $response]);
        }
		return $this->apiSuccess('success', ['data' => $response['body']]);
	} catch(Exception $e) {
		return $this->apiError('error', ['error' => $e->getMessage()]);
	}
}
	public function accountUpdateOldPasswordOauth(Request $request) {
		try{
			$data = [
				"new" => $request->input('new'),
				"confirm" => $request->input('confirm'),
				"old" => $request->input('old'),

			];
			$response = $this->settingService->accountUpdateOldPasswordOauth($data);
			if($response['status'] == 'fail') {
				return $response;
			}
			return $this->apiSuccess('success', ['data' => $response['body']]);
		} catch(Exception $e) {
			return $this->apiError('error', ['error' => $e->getMessage()]);
		}
	}

}
