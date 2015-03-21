<?php
namespace App\Services;
class SettingService {
    private $ndeService;
    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    public function saveDefaultSettingsOauth($params = []){
        $responseReturn = $this->ndeService->callNde('SaveDefaultSettingsOauth', 'putBody', $params);
        return $responseReturn;
    }

    public function accountUpdateOldPasswordOauth($params){
        $responseReturn = $this->ndeService->callNde('AccountUpdateOldPasswordOauth', 'post', $params);
        return $responseReturn;
    }
}
