<?php
namespace App\Services;

class ReportService {
    private $ndeService;
    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function getModificationReportOauth($param){
        $responseReturn = $this->ndeService->callNde('ModificationReportOauth', 'get', $param);
        return $responseReturn;
    }
    public function getReportControlsOauth($param){
        $responseReturn = $this->ndeService->callNde('GetReportControlsOauth', 'get', $param);
        return $responseReturn;
    }

    public function userProfileReportOauth($params) {
        $responseReturn = $this->ndeService->callNde('UserProfileReportOauth', 'get', $params);
        return $responseReturn;
    }

    public function retentionReportOauth($params) {
      $responseReturn = $this->ndeService->callNde('RetentionReportOauth', 'get', $params);
      return $responseReturn;
  }

  public function deletionReportOauth($params) {
    $responseReturn = $this->ndeService->callNde('DeletionReportOauth', 'get', $params);
    return $responseReturn;
  }

  public function scannerUsageReportOauth($params) {
    $responseReturn = $this->ndeService->callNde('ScannerUsageReportOauth', 'get', $params);
    return $responseReturn;
  }

  public function userDocumentHistoryReportOauth($params) {
    $responseReturn = $this->ndeService->callNde('UserDocumentHistoryReportOauth', 'get', $params);
    return $responseReturn;
  }

  public function eventLogReportOauth($params) {
    $responseReturn = $this->ndeService->callNde('EventLogReportOauth', 'get', $params);
    return $responseReturn;
  }

  public function loginTrackingReportOauth($params) {
    $responseReturn = $this->ndeService->callNde('LoginTrackingReportOauth', 'get', $params);
    return $responseReturn;
  }

  public function recordMetadataReportOauth($params) {
    $responseReturn = $this->ndeService->callNde('RecordMetadataReportOauth', 'get', $params);
    return $responseReturn;
  }

  public function networkScannerUsageReportOauth($params) {
    $responseReturn = $this->ndeService->callNde('NetworkScannerUsageReportOauth', 'get', $params);
    return $responseReturn;
  }

  public function getCustomReportControlsOauth($params) {
    $responseReturn = $this->ndeService->callNde('GetCustomReportControlsOauth', 'get', $params);
    return $responseReturn;
  }
}
