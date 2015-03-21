<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use function view;
use Exception;

class NotificationController extends Controller
{
    private $dashboardService;
    private $notificationService;

    public function __construct(DashboardService $dashboardService, NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->dashboardService = $dashboardService;
    }
    

    /**
     * Render Notification Page
     *
     */
    public function renderNotification()
    {
        $responseReturn = $this->dashboardService->getProfiles();
        if ($responseReturn['status'] === 'success') {
            $responseBody = $responseReturn['body'];
            {
                return Inertia::render('notification/Notification', [
                    'programsJson' => json_encode($responseBody->data->profiles),]);
                }
            }
        else {
            return redirect('/login');
        }
    }

    /**
     * Retrieves all notifications
     *
     * @param Request $request
     */
    public function getAllNotificationsOauth(Request $request)
    {
        try {
            $data = [
                "page" => $request->input('page'),
                "page_size" => $request->input('page_size'),
                "sort_by" => $request->input('sort_by'),
            ];
            $response = $this->notificationService->getAllNotificationsOauth($data);
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get information of number unread notification
     *
     * @param Request $request
     */
    public function getUnreadNotificationOauth(Request $request)
    {
        try {
            $response = $this->notificationService->getUnreadNotificationOauth();
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Mark notification as read
     *
     * @param Request $request
     */
    public function readNotificationOauth(Request $request)
    {
        try {
            $params = [
                "notification_ids" => $request->input('notification_ids'),
            ];
            $response = $this->notificationService->readNotificationOauth($params);
            return $this->apiSuccess('success', ['data' => $response['body']->data]);
        } catch(Exception $e) {
            return $this->apiError('error', ['error' => $e->getMessage()]);
        }
    }

}
