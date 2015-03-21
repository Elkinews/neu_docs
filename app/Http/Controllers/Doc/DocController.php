<?php

namespace App\Http\Controllers\Doc;
use App\Http\Controllers\Controller;
use App\Services\DocService;
use Illuminate\Http\Request;
use Exception;

class DocController extends Controller{
  private $docService;

  public function __construct(DocService $docService){
    $this->docService = $docService;
  }

  public function getDocMimeTypeOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id')
        ];
        $responseReturn = $this->docService->getDocMimeTypeOauth($param);
        if ($responseReturn['status'] === 'success') {
            $responseBody = $responseReturn['body'];
            return $this->apiSuccess('success', ['docTypes' => $responseBody->data->docType]);
        } else {
            return $this->apiError('error', ['message' => $responseReturn]);
        }
    } catch(Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
}
}
