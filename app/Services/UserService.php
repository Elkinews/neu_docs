<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class UserService {
    private $ndeService;
    private $loginService;
    public function __construct(NdeService $ndeService, LoginService $loginService)
    {
        $this->ndeService = $ndeService;
        $this->loginService = $loginService;
    }

    public function accountSetNewPasswordOauth($params){
        try {

            $tokenData = $this->loginService->OauthClientCredentials();
            if($tokenData['status'] == 'success') {
                $token = $tokenData['data']->access_token->access_token;

                $ndeUrl = config('nde.nde_url');
                $response = Http::ndeHttpAuth()->withToken($token)->post($ndeUrl . "/api.php?function=AccountSetNewPasswordOauth", $params);

                $responseBody = json_decode($response->body());

                if ($responseBody->status === 'success') {
                    return ['status' => 'success', 'data' => $responseBody];
                }
                else{
                    return ['status' => 'fail', 'message' => $responseBody->message, 'code' => $responseBody->response_code, 'data' => $responseBody->data ];
                }
            } else {
                return ['status' => 'fail', 'message' => 'Getting Client token error!'];
            }
        } catch(Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }

    public function accountReplaceSecurityAnswersOauth($params) {
        $responseReturn = $this->ndeService->callNde('AccountReplaceSecurityAnswersOauth', 'post', $params);
        return $responseReturn;
    }

    public function getAvatarOauth($params) {
        $responseReturn = $this->ndeService->callNde('GetAvatarOauth', 'get', $params);
        return $responseReturn;
    }

    public function getUserIdOauth($params = null) {
        $responseReturn = $this->ndeService->callNde('GetUserIdOauth', 'get', $params);
        return $responseReturn;
    }

    public function uploadAvatarOauth($params) {
        $responseReturn = $this->ndeService->callNde('UploadAvatarOauth', 'post', $params);
        return $responseReturn;
    }
}
