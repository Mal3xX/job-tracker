<?php

declare(strict_types=1);

namespace App\blogic\Dashboard\Controllers;

use App\blogic\Applications\Models\Application;
use App\blogic\Applications\Resources\ApplicationShowResource;
use App\Http\Controllers\Controller;
use App\blogic\Dashboard\Services\DashboardService;
use App\blogic\Dashboard\Resources\DashboardStatsResource;
use Illuminate\Http\JsonResponse;

/**
 * Controller per la gestione della dashboard.
 * Fornisce statistiche sulle candidature dell'utente.
 */
class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}
    
    /**
     * Restituisce le statistiche della dashboard.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', Application::class);
        $stats = $this->dashboardService->getStats();
        if (isset($stats['latest_applications'])) {
            $stats['latest_applications'] = ApplicationShowResource::collection($stats['latest_applications'])->resolve();
        }
        return response()->json([
            'data' => new DashboardStatsResource($stats)
        ]);
    }
}