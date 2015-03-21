<?php

namespace App\Http\Controllers\TabActions;

use App\Http\Controllers\Controller;
use App\Services\ViewDocumentService;
use App\Services\TabActionsService;
use Illuminate\Http\Request;
use Exception;

class TabActionsController extends Controller
{
    private $viewDocumentService;
    private $tabActionsService;

    public function __construct(ViewDocumentService $viewDocumentService, TabActionsService $tabActionsService)
    {
        $this->viewDocumentService = $viewDocumentService;
        $this->tabActionsService = $tabActionsService;
    }

    public function addTabOauth(Request $request)
    {
        try {
            $param = [
                'profile_id' => $request->input('profile_id'),
                'document_id' => $request->input('document_id'),
                'name' => $request->input('name'),
                'box_type' => $request->input('box_type'),
                'predefined' => $request->input('predefined'),
            ];
            $responseReturn = $this->tabActionsService->addTab($param);
            if ($responseReturn['status'] === 'success') {
                $responseBody = $responseReturn['body'];
                return $this->apiSuccess('success', ['data' => $responseBody]);
            } else {
                return $this->apiError('error', ['message' => $responseReturn]);
            }
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function renameTabOauth(Request $request)
    {
        try {
            $param = [
                'profile_id' => $request->input('profile_id'),
                'document_id' => $request->input('document_id'),
                'new_document_id' => $request->input('new_document_id'),
                'is_predefined_tab' => $request->input('is_predefined_tab'),
            ];
            $responseReturn = $this->tabActionsService->renameTab($param);
            if ($responseReturn['status'] === 'success') {
                $responseBody = $responseReturn['body'];
                return $this->apiSuccess('success', ['data' => $responseBody->data]);
            } else {

                return $this->apiError('error', ['message' => $responseReturn]);
            }
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function copyToMyDocumentsOauth(Request $request)
    {
        try {
            $param = [
                'profile_id' => $request->input('profile_id'),
                'old_nuid' => $request->input('old_nuid'),
                'new_nuid' => $request->input('new_nuid'),
            ];
            $responseReturn = $this->viewDocumentService->copyToMyDocumentsOauth($param);
            if ($responseReturn['status'] === 'success') {
                return $this->apiSuccess('success', ['data' => $responseReturn['body']]);
            } else {

                return $this->apiError('error', ['message' => $responseReturn]);
            }
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function deleteTabOauth(Request $request)
    {
        try {
            $param = [
                'profile_id' => $request->input('profile_id'),
                'document_id' => $request->input('document_id'),
            ];
            $responseReturn = $this->tabActionsService->deleteTab($param);
            if ($responseReturn['status'] === 'success') {
                return $this->apiSuccess('success', ['data' => $responseReturn['body']]);
            } else {

                return $this->apiError('error', ['message' => $responseReturn]);
            }
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function changeTabNameOauth(Request $request)
    {
        try {
            $param = [
                'profile_id' => $request->input('profile_id'),
                'document_id' => $request->input('document_id'),
                'new_name' => $request->input('new_name'),
            ];

            $responseReturn = $this->tabActionsService->changeTabNameOauth($param);
            if ($responseReturn['status'] === 'success') {
                return $this->apiSuccess('success', ['data' => $responseReturn['body']]);
            } else {

                return $this->apiError('error', ['message' => $responseReturn]);
            }
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function deletePredefinedTabOauth(Request $request)
    {
        try {
            $param = [
                'profile_id' => $request->input('profile_id'),
                'name' => $request->input('name'),
                'box_type' => $request->input('box_type'),
                'image_group_id' => $request->input('image_group_id'),
            ];
            $responseReturn = $this->tabActionsService->deletePredefinedTabOauth($param);
            if ($responseReturn['status'] === 'success') {
                return $this->apiSuccess('success', ['data' => $responseReturn['body']]);
            } else {

                return $this->apiError('error', ['message' => $responseReturn]);
            }
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }
}
