<?php

namespace App\Http\Controllers\User;
use Exception;
use App\Services\AuthCookieService;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use function view;

class UserController extends Controller{
  private $userService;
  private $authCookieService;

  public function __construct(UserService $userService, AuthCookieService $authCookieService)
  {
    $this->userService = $userService;
    $this->authCookieService = $authCookieService;
  }

  public function accountSetNewPasswordOauth(Request $request) {
    try{
      $data = [
        "new" => $request->input('new'),
        "confirm" => $request->input('confirm'),
        "reset_token" => $request->input('reset_token'),
        "account_id" => $request->input('account_id'),
      ];
      $response = $this->userService->accountSetNewPasswordOauth($data);
      if($response['status'] == 'fail') {
        return $this->apiError('error', ['data' => $response['data']]);
      }
      return $this->apiSuccess('success', ['data' => $response]);
    } catch(Exception $e) {
      return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function accountReplaceSecurityAnswersOauth(Request $request) {
    try {
        $qs = $request->input('answers');
        $questions = [];
        foreach ($qs as $item) {
          array_push($questions, ['question_id' => $item['id'], 'answer' => $item['answer']]);
        }

        $params = [
          'answers' => $questions
        ];

        $response = $this->userService->accountReplaceSecurityAnswersOauth($params);
        // return $this->apiError('error', ['error' => $response]);
        if($response['status'] == 'success') {
          return $this->apiSuccess('success', ['data' => $response]);
        } else {
          return $this->apiError('error', ['error' => $response]);
        }

    } catch (Exception $e) {
        return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function getAvatarOauth(Request $request) {
    try{
      $data = [
        "user_id" => $request->input('user_id'),
      ];
      $response = $this->userService->getAvatarOauth($data);
      if($response['status'] == 'fail') {
        return $this->apiError('error', ['data' => $response]);
      }
      return $this->apiSuccess('success', ['data' => $response]);
    } catch(Exception $e) {
      return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function getUserIdOauth() {
    try{

      $response = $this->userService->getUserIdOauth();
      if($response['status'] == 'fail') {
        return $this->apiError('error', ['data' => $response]);
      }
      return $this->apiSuccess('success', ['data' => $response]);
    } catch(Exception $e) {
      return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function uploadAvatarOauth(Request $request) {
    try{
      $data = [
        "user_id" => $request->input('user_id'),
        "nuid" => $request->input('nuid'),
      ];
      $response = $this->userService->uploadAvatarOauth($data);
      if($response['status'] == 'fail') {
        return $this->apiError('error', ['data' => $response]);
      }
      return $this->apiSuccess('success', ['data' => $response]);
    } catch(Exception $e) {
      return $this->apiError('error', ['error' => $e->getMessage()]);
    }
  }

  public function downloadAvatarForce() {
    $userId = '';
    $nuid = '';
    $path = asset('images/default-avatar.png');
    try{
      $resUserID = $this->userService->getUserIdOauth();
      if($resUserID['status'] == 'fail') {
        return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path]);
      }

      $userId = $resUserID['body']->data->account_id;

      $data = [
        "user_id" => $userId,
      ];

      $resNuid = $this->userService->getAvatarOauth($data);
      if($resNuid['status'] == 'fail') {
        return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path]);
      }

      $nuid = $resNuid['body']->data->nuid;

      $authToken = $this->authCookieService->getAccessToken();
      $accessToken = (string) $authToken;
      $params['profileId'] = 1;
      $response = Http::ndeHttp($accessToken)->asForm()->get(config('nde.FS_URL') . 'single/'.$nuid, $params);
      if($response->successful()) {
        $mask = storage_path().'/app/public/avatar/' . $userId.'-*.*';
        array_map('unlink', glob($mask));


        $png_url = $userId.'-'.$nuid.'.png';
        $savepath =  storage_path().'/app/public/avatar/' . $png_url;

        file_put_contents($savepath, $response);
        $path = asset('storage/avatar/'.$userId.'-'.$nuid.'.png');
        return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path]);
      } else {
        return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path]);
      }

    } catch(Exception $e) {
      return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path, 'error' => $e->getMessage()]);
    }
  }

  public function getProfileAvatarInfo() {
    $userId = '';
    $nuid = '';
    $path = asset('images/default-avatar.png');
    try{
      $resUserID = $this->userService->getUserIdOauth();
      if($resUserID['status'] == 'fail') {
        return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path]);
      }

      $userId = $resUserID['body']->data->account_id;

      $data = [
        "user_id" => $userId,
      ];

      $resNuid = $this->userService->getAvatarOauth($data);
      if($resNuid['status'] == 'fail') {
        return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path]);
      }

      $nuid = $resNuid['body']->data->nuid;

      if (file_exists(storage_path().'/app/public/avatar/'.$userId.'-'.$nuid.'.png')) {
        $path = asset('storage/avatar/'.$userId.'-'.$nuid.'.png');
        return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path]);
      } else {
        $authToken = $this->authCookieService->getAccessToken();
        $accessToken = (string) $authToken;
        $params['profileId'] = 1;
        $response = Http::ndeHttp($accessToken)->asForm()->get(config('nde.FS_URL') . 'single/'.$nuid, $params);
        if($response->successful()) {
          $mask = storage_path().'/app/public/avatar/' . $userId.'-*.*';
          array_map('unlink', glob($mask));
          
          $png_url = $userId.'-'.$nuid.'.png';
          $savepath =  storage_path().'/app/public/avatar/' . $png_url;
          file_put_contents($savepath, $response);
          $path = asset('storage/avatar/'.$userId.'-'.$nuid.'.png');
          return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path]);
        } else {
          return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path]);
        }

      }

    } catch(Exception $e) {
      return $this->apiSuccess('success', ['user_id' => $userId, 'nuid' => $nuid, 'link' => $path, 'error' => $e->getMessage()]);
    }
  }
}
