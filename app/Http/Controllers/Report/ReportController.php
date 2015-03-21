<?php

namespace App\Http\Controllers\Report;

use Exception;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Services\NdeService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use function view;

class ReportController extends Controller{
    private $helpCenterService;
    private $dashboardService;
    private $reportService;

    public function __construct(DashboardService $dashboardService, ReportService $reportService){
        $this->dashboardService = $dashboardService;
        $this->reportService = $reportService;
    }

  public function renderDeletionReport()
  {
      $responseReturn = $this->dashboardService->getProfiles();
      if ($responseReturn['status'] === 'success') {
          $responseBody = $responseReturn['body'];
          // return view('dashboard', ["programsJson" => json_encode($responseBody->data->profiles), 'pageTitle' => 'NDE Dashboard']);
          {
              return Inertia::render('report/deletion/Deletion', [
               'programsJson' => json_encode($responseBody->data->profiles),]);
          }
      }
      else {
          return redirect('/login');
      }
  }


  public function renderModificationReport()
  {
    $responseReturn = $this->dashboardService->getProfiles();
    if ($responseReturn['status'] === 'success') {
        $responseBody = $responseReturn['body'];
        {
            return Inertia::render('report/modification/Modification', [
             'programsJson' => json_encode($responseBody->data->profiles),]);
        }
    }
    else {
        return redirect('/login');
    }
  }

  public function renderMetadataReport()
  {
      $responseReturn = $this->dashboardService->getProfiles();
      if ($responseReturn['status'] === 'success') {
          $responseBody = $responseReturn['body'];
          {
              return Inertia::render('report/metadata/RecordsAndMetadata', [
               'programsJson' => json_encode($responseBody->data->profiles),]);
          }
      }
      else {
          return redirect('/login');
      }
  }

  public function renderUserDocumentHistoryReport()
  {
      $responseReturn = $this->dashboardService->getProfiles();
      if ($responseReturn['status'] === 'success') {
          $responseBody = $responseReturn['body'];
          // return view('dashboard', ["programsJson" => json_encode($responseBody->data->profiles), 'pageTitle' => 'NDE Dashboard']);
          {
              return Inertia::render('report/user-document-history/UserDocumentHistory', [
                  'programsJson' => json_encode($responseBody->data->profiles),]);
              }
          }
      else {
          return redirect('/login');
      }
  }

  public function renderScannerUsageReport()
  {
      $responseReturn = $this->dashboardService->getProfiles();
      if ($responseReturn['status'] === 'success') {
          $responseBody = $responseReturn['body'];
          {
              return Inertia::render('report/scanner-usage/ScannerUsage', [
                  'programsJson' => json_encode($responseBody->data->profiles),]);
              }
          }
      else {
          return redirect('/login');
      }
  }

  public function renderLoginTrackingReport() {
    $responseReturn = $this->dashboardService->getProfiles();
    if ($responseReturn['status'] === 'success') {
        $responseBody = $responseReturn['body'];
        {
            return Inertia::render('report/login-tracking/LoginTracking', [
                'programsJson' => json_encode($responseBody->data->profiles),]);
            }
        }
    else {
        return redirect('/login');
    }
}

public function renderRetentionReport()
  {
      $responseReturn = $this->dashboardService->getProfiles();
      if ($responseReturn['status'] === 'success') {
          $responseBody = $responseReturn['body'];
          {
              return Inertia::render('report/retention/Retention', [
                  'programsJson' => json_encode($responseBody->data->profiles),]);
              }
          }
      else {
          return redirect('/login');
      }
  }

  public function renderUserProfileReport()
  {
      $responseReturn = $this->dashboardService->getProfiles();
      if ($responseReturn['status'] === 'success') {
          $responseBody = $responseReturn['body'];
          {
              return Inertia::render('report/userProfile/UserProfile', [
                  'programsJson' => json_encode($responseBody->data->profiles),]);
              }
          }
      else {
          return redirect('/login');
      }
  }

  public function renderEventLogReport()
  {
      $responseReturn = $this->dashboardService->getProfiles();
      if ($responseReturn['status'] === 'success') {
          $responseBody = $responseReturn['body'];
          {
              return Inertia::render('report/event-log/Event-log', [
                  'programsJson' => json_encode($responseBody->data->profiles),]);
              }
          }
      else {
          return redirect('/login');
      }
  }

  public function renderNetworkScannerUsageReport()
  {
      $responseReturn = $this->dashboardService->getProfiles();
      if ($responseReturn['status'] === 'success') {
          $responseBody = $responseReturn['body'];
          {
              return Inertia::render('report/networkScannerUsage/NetworkScannerUsage.vue', [
                  'programsJson' => json_encode($responseBody->data->profiles),]);
              }
          }
      else {
          return redirect('/login');
      }
  }

  public function getModificationReportOauth(Request $request) {
    try{
        $data = [
            "profile_id" => $request->input('profile_id'),
            "order_by" => $request->input('order_by'),
            "page" => $request->input('page'),
            "page_size" => $request->input('page_size'),
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date'),
            "user_id" => $request->input('user') || 0
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->getModificationReportOauth($data);
        if ($response['status'] === 'success') {
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } else {
            return $this->apiError('error', ['message' => $response]);
        }
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function loginTrackingReportOauth(Request $request) {
    try{
        $data = [
            "profile_id" => $request->input('profile_id') || 1,
            "order_by" => $request->input('order_by'),
            "page" => $request->input('page'),
            "page_size" => $request->input('page_size'),
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date'),
            "user_id" => $request->input('user_id'),
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->loginTrackingReportOauth($data);
        if ($response['status'] === 'success') {
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } else {
            return $this->apiError('error', ['message' => $response]);
        }
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function userDocumentHistoryReportOauth(Request $request) {
    try{
        $data = [
            "profile_id" => $request->input('profile_id'),
            "order_by" => $request->input('order_by'),
            "page" => $request->input('page'),
            "page_size" => $request->input('page_size'),
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date'),
            "user_id" => $request->input('user_id'),
            "activity" => $request->input('activity'),
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->userDocumentHistoryReportOauth($data);
        if ($response['status'] === 'success') {
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } else {
            return $this->apiError('error', ['message' => $response]);
        }
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function recordMetadataReportOauth(Request $request) {
    try{
        $data = [
            "profile_id" => $request->input('profile_id'),
            "order_by" => $request->input('order_by'),
            "page" => $request->input('page'),
            "page_size" => $request->input('page_size'),
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date')
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->recordMetadataReportOauth($data);
        if ($response['status'] === 'success') {
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } else {
            return $this->apiError('error', ['message' => $response]);
        }
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function scannerUsageReportOauth(Request $request) {
    try{
        $data = [
            "profile_id" => $request->input('profile_id'),
            "order_by" => $request->input('order_by'),
            "page" => $request->input('page'),
            "page_size" => $request->input('page_size'),
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date'),
            "user_id" => $request->input('user_id')
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->scannerUsageReportOauth($data);
        if ($response['status'] === 'success') {
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } else {
            return $this->apiError('error', ['message' => $response]);
        }

        // $response = $this->reportService->scannerUsageReportOauth($data);
        // return $this->apiSuccess('success', ['data' => $response['body']->data]);
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function deletionReportOauth(Request $request) {
    try{
        $data = [
            "profile_id" => $request->input('profile_id'),
            "order_by" => $request->input('order_by'),
            "page" => $request->input('page'),
            "page_size" => $request->input('page_size'),
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date'),
            "user_id" => $request->input('user_id')
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->deletionReportOauth($data);
        if ($response['status'] === 'success') {
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } else {
            return $this->apiError('error', ['message' => $response]);
        }
        
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function getReportControlsOauth(Request $request) {
      try{
          $data = [
              "profile_id" => $request->input('profile'),
          ];

          $response = $this->reportService->getReportControlsOauth($data);
          return $this->apiSuccess('success', ['data' => $response['body']->data]);
      } catch(Exception $e) {
          return $this->apiError('error', ['error' => $e->getMessage()]);
      }
  }

  public function retentionReportOauth(Request $request) {
    try{
        $data = [
            "profile_id" => $request->input('profile_id'),
            "order_by" => $request->input('order_by'),
            "page" => $request->input('page'),
            "page_size" => $request->input('page_size'),
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date'),
            "retention_year" => $request->input('retention_year'),
            "include_frozen" => $request->input('include_frozen')
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->retentionReportOauth($data);
        if($response['status'] == 'fail') {
          return $this->apiError('error', ['data' => $response]);
        }
        return $this->apiSuccess('success', ['data' => $response['body']->data]);
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function userProfileReportOauth(Request $request) {
    try{
        $data = [
            "profile_id" => $request->input('profile_id'),
            "order_by" => $request->input('order_by'),
            "page" => $request->input('page'),
            "page_size" => $request->input('page_size'),
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date'),
            "user_id" => $request->input('user_id'),
            "active" => $request->input('active')
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->userProfileReportOauth($data);
        if($response['status'] == 'fail') {
            return $this->apiError('error', ['data' => $response]);
        }

        return $this->apiSuccess('success', ['data' => $response['body']->data]);
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function eventLogReportOauth(Request $request) {
    try{
        $data = [
            "profile_id" => $request->input('profile_id'),
            "order_by" => $request->input('order_by'),
            "page" => $request->input('page'),
            "page_size" => $request->input('page_size'),
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date'),
            "user_id" => $request->input('user_id'),
            "event" => $request->input('event')
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->eventLogReportOauth($data);
        if($response['status'] == 'fail') {
            return $this->apiError('error', ['data' => $response]);
        }

        return $this->apiSuccess('success', ['data' => $response['body']->data]);
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function networkScannerUsageReportOauth(Request $request) {
    try{
        $data = [
            "from_date" => $request->input('from_date'),
            "to_date" => $request->input('to_date'),
            "serial_number" => $request->input('serial_number'),
        ];

        if($request->input('output_csv')) {
            $data += ['output_csv' => $request->input('output_csv')];
        }

        $response = $this->reportService->networkScannerUsageReportOauth($data);
        if($response['status'] == 'fail') {
            return $this->apiError('error', ['data' => $response]);
        }

        return $this->apiSuccess('success', ['data' => $response['body']]);
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function exportToCSV(Request $request) {
    try{
        $data = [
            "exportdata" => $request->input('exportdata'),
            "filename" => $request->input('filename'),
        ];


        $response = $this->dashboardService->exportToCSV($data);
        if($response['status'] == 'fail') {
            return $this->apiError('error', ['data' => $response]);
        }

        return $this->apiSuccess('success', ['data' => $response]);
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function renderOndemand()
    {
        $profileId = $_GET['profileId'];

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
            return Inertia::render('ondemand/index', [
                'profileId' => $profileId,
                'programsJson' => json_encode($responseBody->data->profiles),
                'bulkActions' => json_encode($responseBulkActions['body']->data),
                'pageTitle' => 'NDE On Demand',
                'roles' => $roles,
            ]);
        } else {
            return redirect('/login');
        }
    }

    public function getCustomReportControlsOauth(Request $request) {
        try {
            $data = [
                "profile_id" => $request->input('profile_id')
            ];


            $response = $this->reportService->getCustomReportControlsOauth($data);
            if ($response['status'] == 'fail') {
                return $this->apiError('error', ['data' => $response]);
            }

            return $this->apiSuccess('success', ['data' => $response]);
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }
}



