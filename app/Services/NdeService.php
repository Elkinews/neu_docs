<?php

namespace App\Services;

use App\Models\Login;
use Illuminate\Support\Facades\Http;
use App\Services\AuthCookieService;
use App\Models\AccessToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class NdeService
{
	private $authCookieService;

    public function __construct(AuthCookieService $authCookieService) {
		$this->authCookieService = $authCookieService;
    }

    public function oauthPassword($username, $password){
        $clientId = config('nde.client_id');
        $clientSecret = config('nde.client_secret');
        $ndeUrl = config('nde.nde_url');
        $response = Http::ndeHttpAuth()->post($ndeUrl . "/api.php?function=OauthPassword", [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $username,
            'password' => $password
        ]);

        $responseBody = json_decode($response->body());

        if ($responseBody->status === 'success') {
            if(
              !empty($responseBody->data->reset_token) ||
              !empty($responseBody->data->mfa_token)
            ) {
                return [
                  'status' => 'fail',
                  'message' => $responseBody->message,
                  'code' => $responseBody->response_code,
                  'data' => $responseBody->data,
                ];
            }

            $authToken = $responseBody->data->access_token->access_token;
            $refreshToken = $responseBody->data->access_token->refresh_token;
            $expires = (int) $responseBody->data->access_token->expires;
            return [
                'status' => 'success',
                'access_token' => $authToken,
                'refresh_token' => $refreshToken,
                'token_expires' => $expires,
                'message'=>$responseBody->message,
                'code' => $responseBody->response_code
            ];
        }
        else{
            return ['status' => 'fail', 'message' => $responseBody->message, 'code' => $responseBody->response_code, 'data' => $responseBody->data ];
        }
    }

	private function getKeySessionByAccessToken(string $accessToken) {
		return 'access_token_' . $accessToken;
	}

	private function getAccessTokenByOldAccessToken(string $oldAccessToken) {
		$keySessionAccessToken = $this->getKeySessionByAccessToken($oldAccessToken);
		if (session()->has($keySessionAccessToken)) {
			return session()->get($keySessionAccessToken);
		}
		return NULL;
	}

    public function oauthRefresh($refreshTokenToRefresh){
        $clientId = config('nde.client_id');
        $clientSecret = config('nde.client_secret');
        $ndeUrl = config('nde.nde_url');
        $response = Http::ndeHttpAuth()->post($ndeUrl . "/api.php?function=OauthRefresh", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshTokenToRefresh
        ]);
        $responseBody = json_decode($response->body());
        if (isset($responseBody->status) && $responseBody->status === 'success') {
            $authToken = $responseBody->data->access_token->access_token;
            $refreshToken = $responseBody->data->access_token->refresh_token;
            $expires = (int) $responseBody->data->access_token->expires;

			// Old Auth token 
			$oldAuthToken = $this->authCookieService->getAccessToken();
			$oldAccessToken = $oldAuthToken->getAccessToken();

			$keySessionAccessToken = $this->getKeySessionByAccessToken($oldAccessToken);

            // Set New Auth token Cookie
            $newAuthToken = new AccessToken(
                $authToken,
                $refreshToken,
                $expires,
            );

			$this->authCookieService->set($newAuthToken);
			// Set New Auth token Session
			Session::put($keySessionAccessToken, $newAuthToken);
   
            return [
                'status' => 'success',
                'access_token' => $authToken,
                'refresh_token' => $refreshToken,
                'token_expires' => $expires,
				'message' => 'success',
            ];
        }
        else if (isset($responseBody->message)){
            return ['status' => 'fail', 'message' => $responseBody->message];
        }
        else{
            return ['status' => 'fail', 'message' => $response->body()];
        }
    }

    public function callNde($api, $type, $params = [], $refresh = false){
		
        $authToken = $this->authCookieService->getAccessToken();
        $accessToken = $authToken->getAccessToken();
        $refreshToken = $authToken->getRefreshToken();

        if ($accessToken === ''){
            return ['status' => 'fail', 'message' => 'User not logged in'];
        }

		$newDataAccessToken = $this->getAccessTokenByOldAccessToken($accessToken);

		if (!is_null($newDataAccessToken)) {
			$this->authCookieService->set($newDataAccessToken);
			// Get Token New
			$authToken = $this->authCookieService->getAccessToken();
			$accessToken = $authToken->getAccessToken();
			$refreshToken = $authToken->getRefreshToken();			
		}

        if ($type === 'get') {
            $params['function'] = $api;
            $response = Http::ndeHttp($accessToken)->asForm()->get(config('nde.nde_url') . '/api.php', $params);
        }
        else if ($type === 'post'){
            $params['XDEBUG_SESSION_START'] = 'PHPSTORM';
            $params['function'] = $api;
            $response = Http::ndeHttp($accessToken)->asForm()->post(config('nde.nde_url') . '/api.php', $params);
        }
        else if ($type === 'putBody'){
            $params['function'] = $api;
            $paramsJson = json_encode($params);

          $response = Http::ndeHttp($accessToken)->withBody($paramsJson, 'application/json')->put(config('nde.nde_url') . '/api.php?function=' . $api );

        }
		else if ($type === 'put'){
            $params['XDEBUG_SESSION_START'] = 'PHPSTORM';
            $params['function'] = $api;
            $response = Http::ndeHttp($accessToken)->asForm()->put(config('nde.nde_url') . '/api.php', $params);

        }
        else if ($type === 'postBody'){
            $paramsJson = json_encode($params);
            $response = Http::ndeHttp($accessToken)->withBody($paramsJson, 'application/json')->post(config('nde.nde_url') . '/api.php?function=' . $api);
        }
        else if ($type === 'postFile'){
            $params['function'] = $api;
            $params['json'] = $params['json'];
            $contents = fopen($params['neu_file'], 'r');
            $response = Http::ndeHttp($accessToken)->attach('neu_file',$contents, basename($params['neu_file']))->post(config('nde.nde_url') . '/api.php', $params);

        }
        else if ($type === 'delete'){
            $params['function'] = $api;
            $response = Http::ndeHttp($accessToken)->send('DELETE',config('nde.nde_url') . '/api.php', ['query' => $params]);
        }

        if ($response->successful() && $response->header('Content-Type') == 'text/html; charset=UTF-8'){
            return ['status' => 'success', 'body' => $response->body()];
        }

        $responseBody = json_decode($response->body());


        if ($response->successful() && $response->header('Content-Type') == 'text/csv;;charset=UTF-8'){
            return ['status' => 'success', 'body' => ''];
        }
        else if (isset($responseBody->status) && $responseBody->status === 'success') {
           return ['status' => 'success', 'body' => $responseBody];
        }
        else if (isset($responseBody->message)){
            if (str_contains($responseBody->message, 'Access token is not valid')){
                if ($refresh === false) {
                    $authToken = $this->authCookieService->getAccessToken();
                    $refreshToken = $authToken->getRefreshToken();

                    $tokenBody = $this->oauthRefresh($refreshToken);
                    if ($tokenBody['status'] == 'success' || str_contains($tokenBody['message'], 'Refresh token not found')) {
						if (str_contains($tokenBody['message'], 'Refresh token not found')) {
							usleep(10000);
						}
						$responseNew = $this->callNde($api, $type, $params, true);
                        return $responseNew;
                    } else {
                        return ['status' => 'fail', 'message' => $tokenBody['message']];
                    }
                }
                else{
                    return ['status' => 'fail', 'message' => $responseBody->message];
                }
            }
            else{
                return ['status' => 'fail', 'message' => $responseBody->message, 'response' => $responseBody ];
            }
        }
        else{
            return ['status' => 'fail', 'message' => $response->body()];
        }
        return $responseBody;
    }
}
