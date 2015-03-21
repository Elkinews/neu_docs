<?php

namespace App\Http\Controllers\HelpCenter;
use App\Http\Controllers\Controller;
use App\Services\HelpCenterService;
use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use function view;
use Exception;
class HelpCenterController extends Controller{
  private $helpCenterService;
  private $dashboardService;

  public function __construct(DashboardService $dashboardService, HelpCenterService $helpCenterService){
    $this->helpCenterService = $helpCenterService;
    $this->dashboardService = $dashboardService;
  }
  public function renderScreen()
  {
    $responseReturn = $this->dashboardService->getProfiles();
    if ($responseReturn['status'] === 'success') {
      $responseBody = $responseReturn['body'];
      {
        return Inertia::render('help-center/index', [
          'programsJson' => json_encode($responseBody->data->profiles),]);
      }
    } else {
      return redirect('/login');
    }
  }

  public function renderScreenBasics()
  {
    $responseReturn = $this->dashboardService->getProfiles();
    if ($responseReturn['status'] === 'success') {
      $responseBody = $responseReturn['body'];
      {
        return Inertia::render('help-center/HelpCenterBasics', [
          'programsJson' => json_encode($responseBody->data->profiles),]);
      }
    } else {
      return redirect('/login');
    }
  }

  public function renderScreenIssues()
  {
    $responseReturn = $this->dashboardService->getProfiles();
    if ($responseReturn['status'] === 'success') {
      $responseBody = $responseReturn['body'];
      {
        return Inertia::render('help-center/HelpCenterIssues', [
          'programsJson' => json_encode($responseBody->data->profiles),]);
      }
    } else {
      return redirect('/login');
    }
  }

  public function renderFAQ()
  {
    $responseReturn = $this->dashboardService->getProfiles();
    if ($responseReturn['status'] === 'success') {
      $responseBody = $responseReturn['body'];
      {
        return Inertia::render('faq', [
          'programsJson' => json_encode($responseBody->data->profiles),]);
      }
    } else {
      return redirect('/login');
    }
  }

  public function getHelpContentsOauth(Request $request) {
    try {
        $responseReturn = $this->helpCenterService->getHelpContentsOauth();
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

  public function getFAQContentsOauth(Request $request) {
    try {
        $responseReturn = $this->helpCenterService->getFAQContentsOauth();
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
}
