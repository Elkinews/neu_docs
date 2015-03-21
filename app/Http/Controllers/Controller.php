<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    final protected function apiSuccess(string $message, array $payload = [], int $httpResponseCode = 200) {
        return $this->apiResponse($message, $payload, $httpResponseCode);
    }

    final protected function apiError(string $message, array $payload = [], int $httpResponseCode = 500,
                                      int $internalCode = 500) {
        $payload['error_code']  = $internalCode;
        return $this->apiResponse($message, $payload, $httpResponseCode);
    }

    final protected function apiResponse(string $message, array $payload, int $httpResponseCode) {
        return response()->json(['message' => $message, 'data' => $payload], $httpResponseCode);
    }
}
