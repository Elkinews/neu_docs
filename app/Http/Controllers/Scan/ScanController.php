<?php

namespace App\Http\Controllers\Scan;
use App\Http\Controllers\Controller;
use App\Services\ScanService;
use Illuminate\Http\Request;
use Exception;

class ScanController extends Controller{
  private $scanService;

  public function __construct(ScanService $scanService){
    $this->scanService = $scanService;
  }
  
  public function viewScanOptionsOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id'),
        ];
        $responseReturn = $this->scanService->viewScanOptionsOauth($param);
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

  public function getPredefinedBookmarkOauth(Request $request) {
    try {
      $param = [
        'profile_id' => $request->input('profile_id'),
        'doc_id' => $request->input('doc_id'),
        'boxtype' => $request->input('boxtype')
      ];

      $responseReturn = $this->scanService->getPredefinedBookmarkOauth($param);
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