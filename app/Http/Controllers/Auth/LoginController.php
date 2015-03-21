<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\AccessToken;
use App\Services\NdeService;
use App\Services\LoginService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use function view;
use App\Services\AuthCookieService;

class LoginController extends Controller
{

    private $ndeService;
    private $loginService;
    private $authCookieService;

    public function __construct(NdeService $ndeService, LoginService $loginService, AuthCookieService $authCookieService)
    {
        $this->ndeService = $ndeService;
        $this->loginService = $loginService;
        $this->authCookieService = $authCookieService;
    }

    public function loginScreen(){
        return Inertia::render('login/index', [
            'loginUri' => route('doLogin')
        ]);
    }

    public function login(Request $request){
        try{
            $username = $request->input('username');
            $password = $request->input('password');
            $result = $this->ndeService->oauthPassword($username, $password);

            if($result['message'] == 'Password expired') {
                return $this->apiSuccess('Password expired', ['login' => false, 'data'=> $result]);
            }

            if ($result['status'] === 'success') {

                $newAuthToken = new AccessToken(
                    $result['access_token'],
                    $result['refresh_token'],
                    $result['token_expires'],
                );
                // Set Auth token Cookie
                $this->authCookieService->set($newAuthToken);
                $result_is_change_pwd = $this->loginService->getPasswordChangeRequiredOauth();

                return $this->apiSuccess(
                    'login success',
                    [
                        'login' => true,
                        'change_password' => $result_is_change_pwd['body']->data->change_password
                    ]);
            }
            else {
                if(!empty($result['data']->reset_token)) {
                    return $result;
                }

                if($result['message'] == 'Account suspended') {
                    return $this->apiError('Authentication failed: Account is SUSPENDED. Please contact <a href="mailto:'.config('nde.nde_support_email').'" style="color: #c32c39;">Administrator</a>.', ['login' => false]);
                }

                if($result['code'] == '401') {
                    return $this->apiError('Authentication failed -- please try again.', ['login' => false, 'data' => $result]);
                }

                if($result['code'] == '403') {
                    // if($result['data'] -> code == "N402") {
                        return $this->apiError($result['message'], ['login' => false, 'data' => $result]);
                    // }

                }

                if (array_key_exists('data', $result) && array_key_exists('mfa_token', $result['data'])) {
                    session(['mfa_token' => $result['data']->mfa_token]);
                }

                return $this->apiError($result['message'], ['login' => false, 'data' => $result]);
            }
        } catch(Exception $e) {
            return $this->apiError('Authentication failed -- please try again.', ['login' => false, 'data' => $e->getMessage()]);
        }

    }

    public function resetPasswordScreen() {
        return Inertia::render('login/resetPassword', [
            'resetUri' => route('doLogin')
        ]);
    }

    public function resetSecurityScreen() {
        return Inertia::render('login/resetSecurity', [
            'resetUri' => route('doLogin')
        ]);
    }

    public function changePasswordScreen() {
        return Inertia::render('login/changePassword', [
            'changePWDUri' => route('doLogin')
        ]);
    }

    public function getCaptchaForm() {
        return view('captcha');
    }

    public function captchaScreen(Request $request) {
        $request->session()->regenerate();
        return Inertia::render('login/captcha', [
            'changePWDUri' => route('doLogin')
        ]);
    }

    public function doVerifyCaptcha(Request $request) {
        $code = $request->input('CaptchaCode');

        try {

            $isHuman = captcha_validate($code);
            // $isHuman = simple_captcha_validate($code, $captchaId);
            // return $this->apiSuccess('verify success', ['verify' => true]);
            if ($isHuman) {
                // TODO: Captcha validation passed, perform protected  action
                return $this->apiSuccess('verify success', ['verify' => $isHuman, 'data' => $request->input()]);

            } else {
                // TODO: Captcha validation failed, show error message
                return $this->apiError('Incorrect Code', ['verify' => $isHuman, 'data' => $request->input()]);
            }
        } catch(Exception $e) {
            return $this->apiError('verify success', ['verify' => false, 'data' => $e->getMessage()]);
        }
        
    }

    public function mfaMobile() {
        return Inertia::render('login/mfa', [
            'mfaType' => 'mobile'
        ]);
    }

    public function mfaEmail() {
        return Inertia::render('login/mfa', [
            'mfaType' => 'email'
        ]);
    }

    public function mfaBoth() {
        $mfa_token = session('mfa_token');
        $response = $this->loginService->getMfaOauth($mfa_token);

       if($response['status'] == 'success') {
            return Inertia::render('login/mfa', [
                'mfaType' => 'both',
                'data' => $response['data'],
                'token' => $mfa_token
            ]);
        } else {
            return redirect()->route('login');
        }
    }

    public function getMfaOauth(Request $request) {
        try{
            $mfa_token = $request->input('mfa_token');

            $response = $this->loginService->getMfaOauth($mfa_token);
            if($response['status'] == 'success') {
                return $this->apiSuccess('success', ['data' => $response['data']]);
            } else {
                return $this->apiError('error', ['error' => $response['message']]);
            }

        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function accountGetPasswordResetQuestionsOauth(Request $request) {
        try{
            $username = $request->input('username');

            $response = $this->loginService->accountGetPasswordResetQuestionsOauth($username);
            if($response['status'] == 'success') {
                return $this->apiSuccess('success', ['questions' => $response['data']]);
            } else {
                if($response['message'] == 'Unable to process request') {
                    return $this->apiError('error', ['error' => 'Invalid Username']);
                } else {
                    return $this->apiError('error', ['error' => $response['message']]);
                }

            }

        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function oauthMfa(Request $request) {
        try {
            $mfa_token = $request->input('mfa_token');
            $otp = $request->input('otp');
            $is_email = $request->input('is_email');

            $response = $this->loginService->oauthMfa($mfa_token, $otp, $is_email);

            if($response['status'] == 'success') {
                // Set Auth token data
                $newAuthToken = new AccessToken(
                    $response['access_token'],
                    $response['refresh_token'],
                    $response['token_expires'],
                );
                // Set Auth token Cookie
                $this->authCookieService->set($newAuthToken);

                return $this->apiSuccess('success', ['data' => $response]);
            } else {
                session(['mfa_token' => '']);
                return $this->apiError('error', ['error' => $response]);
            }

        } catch(Exception $e) {
            session(['mfa_token' => '']);
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function accountConfirmSecurityAnswersOauth(Request $request) {
        try {
            $qs = $request->input('questions');
            $username = $request->input('username');
            $questions = [];
            foreach ($qs as $item) {
                array_push($questions, ['question_id' => $item['question_id'], 'answer' => $item['question_answer']]);
            }

            $params = [
                'username' => $username,
                'answers' => $questions
            ];

            $response = $this->loginService->accountConfirmSecurityAnswersOauth($params);

            if($response['status'] == 'success') {
                return $this->apiSuccess('success', ['data' => $response]);
            } else {
                return $this->apiError('error', ['error' => $response]);
            }

        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }

    }

    public function getAccessToken() {
        $authToken = $this->authCookieService->getAccessToken();
        $accessToken = $authToken->getAccessToken();
        $refreshToken = $authToken->getRefreshToken();
        $expiresAt = $authToken->getExpiresAt();
        if ($accessToken === ''){
            return $this->apiError('error', ['error' => 'You are not logged in!']);
        }

        return $this->apiSuccess(
            'success', [
                'token' => $accessToken,
                'refreshtoken' => $refreshToken,
                'expiresAt' => $expiresAt,
            ]);
    }

    public function getAccessTokenRefresh() {
        try {
            $authToken = $this->authCookieService->getAccessToken();
            $refreshToken = $authToken->getRefreshToken();
            $result = $this->ndeService->oauthRefresh($refreshToken);

            if ($result['refresh_token'] === ''){
                return $this->apiError('error', ['error' => 'You are not logged in!']);
            }
            $newAuthToken = new AccessToken(
                $result['access_token'],
                $result['refresh_token'],
                $result['token_expires'],
            );
            // Set Auth token Cookie
            $this->authCookieService->set($newAuthToken);

            return $this->apiSuccess(
                'success',
                [
                    'token' => $result['access_token'],
                    'refreshtoken' =>  $result['refresh_token'],
                ]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function endOauthSession() {
        try {
            $authToken = $this->authCookieService->getAccessToken();

            $params = [
                'access_token' => (string) $authToken,
            ];

            $response = $this->loginService->endOauthSession($params);

            if($response['status'] == 'success') {
                session(['username' => '']);
                // Forget ncore_auth cookie
                $this->authCookieService->expireCookie();

                return $this->apiSuccess('success', ['data' => $response]);
            } else {
                return $this->apiError('error', ['error' => $response]);
            }
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function getSystemSettingOauth() {
        try {
            $response = $this->loginService->getSystemSettingOauth();

            if($response['status'] == 'success') {
                return $this->apiSuccess('success', ['data' => $response]);
            } else {
                return $this->apiError('error', ['error' => $response]);
            }
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    public function resendMfaEmailOauth(Request $request) {
        try {
            $mfa_token = $request->input('mfa_token');
            $response = $this->loginService->resendMfaEmailOauth($mfa_token);
            
            if($response['status'] == 'success') {
                return $this->apiSuccess('success', ['data' => $response]);
            } else {
                return $this->apiError('error', ['error' => $response]);
            }
        } catch (Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }
}
