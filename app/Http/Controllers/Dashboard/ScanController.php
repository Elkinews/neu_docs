<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\NdeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use function view;


class ScanController extends Controller
{

    private $dashboardService;

    public function __construct(DashboardService $dashboardService){
        $this->dashboardService = $dashboardService;
    }
}
