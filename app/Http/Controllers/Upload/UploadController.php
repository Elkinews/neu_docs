<?php

namespace App\Http\Controllers\Upload;

use Exception;
use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
  private $uploadService;

  public function __construct(UploadService $uploadService)
  {
    $this->uploadService = $uploadService;
  }


  public function uploadFileMetadataOauth(Request $request)
  {
    try {
      $data = [
        "scan" => $request->input('scan') || false,
        "profileId" => $request->input('profileId'),
        "boxType" => $request->input('boxType'),
        "nuid" => $request->input('nuid'),
        "filePath" => $request->input('filePath'),
        "docId" => $request->input('docId'),
        "file_size" => $request->input('file_size'),
        "page_count" => $request->input('page_count'),
      ];

      if ($request->input('annotation_location')) {
        $data += ['annotation_location' => $request->input('annotation_location')];
      }

      if ($request->input('annotation_type_code')) {
        $data += ['annotation_type_code' => $request->input('annotation_type_code')];
      }

      if ($request->input('annotation_input')) {
        $data += ['annotation_input' => $request->input('annotation_input')];
      }

      if ($request->input('folder_id')) {
        $data += ['folder_id' => $request->input('folder_id')];
      }

      $response = $this->uploadService->uploadFileMetadataOauth($data);
      if ($response['status'] == 'fail') {
        return $this->apiError('error', ['data' => $response['response']]);
      }
      return $this->apiSuccess('success', ['data' => $response]);
    } catch (Exception $e) {
      return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function getAnnotationTypesOauth(Request $request)
  {
    try {
      $param = [
        'profile_id' => $request->input('profile_id')
      ];
      $response = $this->uploadService->getAnnotationTypesOauth($param);
      if ($response['status'] === 'success') {
        return $this->apiSuccess('success', ['data' => $response['body']]);
      } else {
        return $this->apiError('error', ['message' => $response]);
      }
    } catch (Exception $e) {
      return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }
}
