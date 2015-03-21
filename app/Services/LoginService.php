<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class LoginService
{
    private $ndeService;

    public function __construct(NdeService $ndeService) {
        $this->ndeService = $ndeService;
    }

    public function getPasswordChangeRequiredOauth(){
        $responseReturn = $this->ndeService->callNde('GetPasswordChangeRequiredOauth', 'get', []);
        return $responseReturn;
    }

    public function getMfaOauth($mfa_token){
        try {

            $tokenData = $this->OauthClientCredentials();
            if($tokenData['status'] == 'success') {
                $token = $tokenData['data']->access_token->access_token;
                $ndeUrl = config('nde.nde_url');
                $response = Http::withToken($token)->get($ndeUrl . "/api.php?function=GetMfaOauth&mfa_token=".$mfa_token);

                $responseBody = json_decode($response->body());
                if (property_exists($responseBody, 'status') && $responseBody->status === 'success') {
                    return ['status' => 'success', 'data' => $responseBody->data];
                }
                else if (property_exists($responseBody, 'message')){
                    return ['status' => 'fail', 'message' => $responseBody->message];
                }
                else{
                    return ['status' => 'fail', 'message' => $response->body()];
                }
            } else {
                return ['status' => 'fail', 'message' => 'Getting Client token error!'];
            }
        } catch(Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }

        
    }

    public function accountGetPasswordResetQuestionsOauth($username) {
        try {

            $tokenData = $this->OauthClientCredentials();
            if($tokenData['status'] == 'success') {
                $token = $tokenData['data']->access_token->access_token;
                $ndeUrl = config('nde.nde_url');
                $response = Http::withToken($token)->get($ndeUrl . "/api.php?function=AccountGetPasswordResetQuestionsOauth&username=".$username);
        
                $responseBody = json_decode($response->body());
                if ($responseBody->status === 'success') {
        
                    return ['status' => 'success', 'data' => $responseBody->data->questions];
                }
                else{
                    return ['status' => 'fail', 'message' => $responseBody->message, 'code' => $responseBody->response_code, '  ' => $responseBody->data ];
                }

            } else {
                return ['status' => 'fail', 'message' => 'Getting Client token error!'];
            }
            
        } catch(Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }

    public function oauthMfa($mfa_token, $otp, $is_email){
        $clientId = config('nde.client_id');
        $clientSecret = config('nde.client_secret');
        $ndeUrl = config('nde.nde_url');
        $response = Http::ndeHttpAuth()->post($ndeUrl . "/api.php?function=OauthMfa", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'mfa_token' => $mfa_token,
            'otp' => $otp,
            'is_email' => $is_email
        ]);
        $responseBody = json_decode($response->body());

        if ($responseBody->status === 'success') {
            $authToken = $responseBody->data->access_token->access_token;
            $refreshToken = $responseBody->data->access_token->refresh_token;
            $expires = (int) $responseBody->data->access_token->expires;
            return [
                'status' => 'success',
                'access_token' => $authToken,
                'refresh_token' => $refreshToken,
                'token_expires' => $expires,
            ];
        }
        else{
            return ['status' => 'fail', 'message' => $responseBody->message, 'code' => $responseBody->response_code, 'data' => $responseBody->data ];
        }
    }

    public function accountConfirmSecurityAnswersOauth($params) {

        try {

            $tokenData = $this->OauthClientCredentials();
            if($tokenData['status'] == 'success') {
                $token = $tokenData['data']->access_token->access_token;

                $ndeUrl = config('nde.nde_url');
                $response = Http::ndeHttpAuth()->withToken($token)->post($ndeUrl . "/api.php?function=AccountConfirmSecurityAnswersOauth", $params);
                $responseBody = json_decode($response->body());
                if ($responseBody->status === 'success') {
                    return ['status' => 'success', 'data' => $responseBody->data];
                }
                else{
                    return ['status' => 'fail', 'data' => $responseBody];
                }
            } else {
                return ['status' => 'fail', 'message' => 'Getting Client token error!'];
            }
        } catch(Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }

    }

    public function endOauthSession($param){
        $responseReturn = $this->ndeService->callNde('EndOauthSession', 'post', $param);
        return $responseReturn;
    }

    public function getSecurityQuestionsStatusOauth($params = []){
        $responseReturn = $this->ndeService->callNde('GetSecurityQuestionsStatusOauth', 'get', $params);
        return $responseReturn;
    }

    public function getSystemSettingOauth() {
        $responseReturn = $this->ndeService->callNde('GetSystemSettingOauth', 'get', ['name' => 'session_timeout']);
        return $responseReturn;
    }

    public function resendMfaEmailOauth($mfa_token) {
        try {

            $tokenData = $this->OauthClientCredentials();
            if($tokenData['status'] == 'success') {
                $token = $tokenData['data']->access_token->access_token;

                $ndeUrl = config('nde.nde_url');
                $clientId = config('nde.client_id');
                $clientSecret = config('nde.client_secret');
                $params = [
                    'mfa_token' => $mfa_token,
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret
                ];
                $response = Http::ndeHttpAuth()->withToken($token)->post($ndeUrl . "/api.php?function=ResendMfaEmailOauth", $params);
                $responseBody = json_decode($response->body());
                if ($responseBody->status === 'success') {
                    return ['status' => 'success', 'data' => $responseBody->data];
                }
                else{
                    return ['status' => 'fail', 'data' => $responseBody];
                }
            } else {
                return ['status' => 'fail', 'message' => 'Getting Client token error!'];
            }
        } catch(Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }
    }

    public function OauthClientCredentials() {

        try {
            $ndeUrl = config('nde.nde_url');

            $clientId = config('nde.client_id');
            $clientSecret = config('nde.client_secret');
            $params = [
                'client_id' => $clientId,
                'client_secret' => $clientSecret
            ];

            $response = Http::ndeHttpAuth()->post($ndeUrl . "/api.php?function=OauthClientCredentials", $params);
            $responseBody = json_decode($response->body());
            if ($responseBody->status === 'success') {
                return ['status' => 'success', 'data' => $responseBody->data];
            }
            else{
                return ['status' => 'fail', 'data' => $responseBody];
            }
        } catch(Exception $e) {
            return ['status' => 'fail', 'message' => $e->getMessage()];
        }

    }
}
