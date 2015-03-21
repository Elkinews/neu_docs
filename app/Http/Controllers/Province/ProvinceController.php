<?php

namespace App\Http\Controllers\Province;

use Exception;
use App\Http\Controllers\Controller;
use App\Services\ProvinceService;
use Illuminate\Http\Request;
use Inertia\Inertia;



class ProvinceController extends Controller {
    private $provinceService;

    public function __construct(ProvinceService $provinceService){
        $this->provinceService = $provinceService;
    }

    public function getCollectedProvincePermissions(Request $request) {
        try{
            $provinces_ids = $request->input('provinces_ids');
            $ids = [];
            foreach ($provinces_ids as $item) {
                $ids[] = $item;
            }

            $data = [
                'provinces_ids' =>$ids,
            ];

            $response = $this->provinceService->getCollectedProvincePermissions($data);
            if ($response['status'] === 'success') {
                return $this->apiSuccess('success', ['data' => $response['body']->data]);
            } else {
                return $this->apiError('error', ['message' => $response]);
            }
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }

      }
}
