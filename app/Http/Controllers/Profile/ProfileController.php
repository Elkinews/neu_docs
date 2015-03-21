<?php

namespace App\Http\Controllers\Profile;

use Exception;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\MyDocumentService;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Inertia\Inertia;



class ProfileController extends Controller {
    private $dashboardService;
    private $myDocumentService;
    private $profileService;

    public function __construct(DashboardService $dashboardService, MyDocumentService $myDocumentService, ProfileService $profileService){
        $this->dashboardService = $dashboardService;
        $this->myDocumentService = $myDocumentService;
        $this->profileService = $profileService;
    }

    public function renderMyDocuments() {
        $responseReturn = $this->dashboardService->getProfiles();
        if ($responseReturn['status'] === 'success') {
            $responseBody = $responseReturn['body'];
            $profileId = $responseBody->data->profiles[0]->id;
            {
                return Inertia::render('my-documents/index', [
                 'programsJson' => json_encode($responseBody->data->profiles),
                 'profileId' => $profileId]);
            }
        }
        else {
            return redirect('/login');
        }
    }

    public function getDownloadOauth(Request $request) {
      try{
        $responseReturn = $this->dashboardService->getProfiles();
        if ($responseReturn['status'] === 'success') {
          $responseBody = $responseReturn['body'];
          $profileId = $responseBody->data->profiles[0]->id;
          try{
            $data = [
                "profile_id" => $profileId,
                "page_size" => $request->input('page_size'),
                "page" => $request->input('page'),
            ];

            $response = $this->myDocumentService->getDownloadOauth($data);
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
          } catch(Exception $e) {
              return $this->apiError('error', ['error' => $e->getMessage()]);
          }
        }
      } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
      }
    }

    public function deleteDownloadOauth(Request $request) {
      try{
          $data = [
              "profile_id" => $request->input('profile_id'),
              "download_id" => $request->input('download_id'),
          ];

          $response = $this->myDocumentService->deleteDownloadOauth($data);
          return $this->apiSuccess('success', ['data' => $response['body']->data]);
      } catch(Exception $e) {
          return $this->apiError('error', ['error' => $e->getMessage()]);
      }

    }

    public function getSharableAccountsOauth(Request $request) {
      try{
          $data = [];

          $response = $this->myDocumentService->getSharableAccountsOauth($data);
          return $this->apiSuccess('success', ['data' => $response]);
      } catch(Exception $e) {
          return $this->apiError('error', ['error' => $e->getMessage()]);
      }

    }

    public function shareMyDocsOauth(Request $request) {
      try{
        $Searchitems = $request->input('receivers');
        $fieldsParam = [];
        foreach ($Searchitems as $item) {
            $fieldsParam[] = $item;
        }
          $data = [
              'filename' => $request->input('filename'),
              'receivers' => $fieldsParam
          ];

          $response = $this->myDocumentService->shareMyDocsOauth($data);
          return $this->apiSuccess('success', ['data' => $response]);
      } catch(Exception $e) {
          return $this->apiError('error', ['error' => $e->getMessage()]);
      }

    }

    public function getProfilesOauth(Request $request) {
        try{
            $modules_input = $request->input('modules');
            $modules = [];
            foreach ($modules_input as $item) {
                $modules[] = $item;
            }

            $data = [
                'modules' =>$modules,
            ];

            $response = $this->profileService->getProfilesOauth($data);
            return $this->apiSuccess('success', ['data' => $response]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }

      }
}
