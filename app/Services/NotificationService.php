<?php

namespace App\Services;

class NotificationService
{
    private $ndeService;

    /**
     * Constructor
     *
     * @param NdeService $ndeService
     */
    public function __construct(NdeService $ndeService)
    {
        $this->ndeService = $ndeService;
    }

    /**
     * Retrieves all notifications
     *
     * @param array $params
     */
    public function getAllNotificationsOauth($params)
    {
        $responseReturn = $this->ndeService->callNde('GetAllNotificationsOauth', 'get', $params);
        return $responseReturn;
    }
    
    /**
     * Get information of number unread notification that belong to the access_token
     *
     * @param array $params
     */
    public function getUnreadNotificationOauth()
    {
        $responseReturn = $this->ndeService->callNde('GetUnreadNotificationOauth', 'get', []);
        return $responseReturn;
    }
    
    /**
     * Mark notification(s) as read
     *
     * @param array $params
     */
    public function readNotificationOauth($params)
    {
        $responseReturn = $this->ndeService->callNde('ReadNotificationOauth', 'post',  $params);
        return $responseReturn;
    }

}
