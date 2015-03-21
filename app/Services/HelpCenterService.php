<?php
namespace App\Services;
class HelpCenterService {
    private $ndeService;
    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

	public function getHelpContentsOauth() {
		$responseReturn = $this->ndeService->callNde('GetHelpContentsOauth', 'get', []);
		return $responseReturn;
	}

  public function getFAQContentsOauth() {
		$responseReturn = $this->ndeService->callNde('GetFAQContentsOauth', 'get', []);
		return $responseReturn;
	}
}
