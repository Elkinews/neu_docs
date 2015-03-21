<?php

namespace App\Http\Controllers\ViewDocument;
use App\Http\Controllers\Controller;
use App\Services\ViewDocumentService;
use App\Http\Requests\ViewDocument\GetFileLockStatusOauthRequest;
use App\Http\Requests\ViewDocument\LockFileOauthRequest;
use Illuminate\Http\Request;
use Exception;

class ViewDocumentController extends Controller{
  private $viewDocumentService;

  public function __construct(ViewDocumentService $viewDocumentService){
    $this->viewDocumentService = $viewDocumentService;
  }
  
  public function getTabFilesOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id'),
            'image_id' => $request->input('image_id'),
            'page' => $request->input('page'),
            'page_size' => $request->input('page_size')
        ];

        if($request->input('folder_id')) {
            $param += ['folder_id' => $request->input('folder_id')];
        }

        if($request->input('order_by') && $request->input('order')) {
            $param += ['order_by' => $request->input('order_by'), 'order' => $request->input('order')];
        }

        $responseReturn = $this->viewDocumentService->getTabFilesOauth($param);
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

  public function getTabFilesHistoryOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id'),
            'bookmark_id' => $request->input('bookmark_id'),
            'page' => $request->input('page'),
            'page_size' => $request->input('page_size')
        ];
        $responseReturn = $this->viewDocumentService->getTabFilesHistoryOauth($param);
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

  public function updateDocumentPieceOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id'),
            'old_nuid' => $request->input('old_nuid'),
            'new_nuid' => $request->input('new_nuid'),
            'new_file_path' => $request->input('new_file_path'),
            'new_file_size' => $request->input('new_file_size'),
            'new_page_count' => $request->input('new_page_count')
        ];
        $responseReturn = $this->viewDocumentService->updateDocumentPieceOauth($param);
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

  public function getLinksOauth(Request $request) {
    try {
        $param = [
            'nuid' => $request->input('nuid'),
        ];
        $responseReturn = $this->viewDocumentService->getLinksOauth($param);
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

  public function cancelLinkOauth(Request $request) {
    try {
        $param = [
            'nuid' => $request->input('nuid'),
        ];
        $responseReturn = $this->viewDocumentService->cancelLinkOauth($param);
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

  public function addTempLinkOauth(Request $request) {
    try {
        $param = [
            'nuid' => $request->input('nuid'),
            'temp_nuid' => $request->input('temp_nuid'),
            'expiration_time' => $request->input('expiration_time'),
            'note' => $request->input('note')

        ];
        $responseReturn = $this->viewDocumentService->addTempLinkOauth($param);
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

  public function getFileLockStatusOauth(GetFileLockStatusOauthRequest $request)
  {
    try {
        $param = [
            'nuid' => $request->input('nuid'),
            'profile_id' => $request->input('profile_id')

        ];
        $responseReturn = $this->viewDocumentService->getFileLockStatusOauth($param);
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
  
  public function lockFileOauth(LockFileOauthRequest $request)
  {
    try {
        $param = [
            'nuid' => $request->input('nuid'),
            'profile_id' => $request->input('profile_id'),
            'unlock_file' => $request->input('unlock_file')
        ];
        $responseReturn = $this->viewDocumentService->lockFileOauth($param);
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

  public function getFolderPredefinedOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id'),
            'level_no' => $request->input('level_no')
        ];
        $responseReturn = $this->viewDocumentService->getFolderPredefinedOauth($param);
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

  public function addFolderOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id'),
            'folder_name' => $request->input('folder_name'),
            'document_id' => $request->input('document_id'),
        ];

        if( $request->input('parent_folder_id') ) {
            $param += ['parent_folder_id' => $request->input('parent_folder_id')];
        }
        $responseReturn = $this->viewDocumentService->addFolderOauth($param);
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

  public function deleteFolderOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id'),
            'folder_id' => $request->input('folder_id'),
            'document_id' => $request->input('document_id'),
        ];

        $responseReturn = $this->viewDocumentService->deleteFolderOauth($param);
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

  public function renameFolderOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id'),
            'new_name' => $request->input('new_name'),
            'folder_id' => $request->input('folder_id'),
            'document_id' => $request->input('document_id'),
        ];

        $responseReturn = $this->viewDocumentService->renameFolderOauth($param);
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

  public function updatePieceFilenameOauth(Request $request) {
    try {
        $param = [
            'profile_id' => $request->input('profile_id'),
            'new_name' => $request->input('new_name'),
            'nuid' => $request->input('nuid'),
            'document_id' => $request->input('document_id'),
        ];

        $responseReturn = $this->viewDocumentService->updatePieceFilenameOauth($param);
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