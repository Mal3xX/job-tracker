<?php

declare(strict_types=1);

namespace App\blogic\Dashboard\Services;

use App\blogic\Applications\Models\Application;
use App\blogic\_Shared\Traits\GetCurrentUserId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Service per il calcolo delle statistiche della dashboard.
 */
class DashboardService
{
    use GetCurrentUserId;
    /**
     * Invalida la cache delle statistiche per un utente.
     *
     * @param int $userId
     */
    public static function clearStatsCache(int $userId): void
    {
        Cache::forget('dashboard_stats_' . $userId);
    }

    /**
     * Recupera tutte le statistiche per la dashboard.
     *
     * @return array
     */
    public function getStats(): array
    {
        $userId = $this->getCurrentUserId();

        $cached = Cache::remember('dashboard_stats_' . $userId, 3600, function () use ($userId) {
            return [
                'total_applications' => $this->getTotalApplications($userId),
                'current_month_applications' => $this->getCurrentMonthApplications($userId),
                'previous_month_applications' => $this->getPreviousMonthApplications($userId),
                'trend' => $this->getLast6MonthsTrend($userId),
                'status_distribution' => $this->getStatusDistribution($userId),
                'average_response_time' => null,
            ];
        });

        $cached['latest_applications'] = $this->getLatestApplications($userId);

        return $cached;
    }

    /**
     * Totale candidature dell'utente.
     */
    private function getTotalApplications(int $userId): int
    {
        return Application::where('user_id', $userId)->count();
    }

    /**
     * Candidature del mese corrente.
     */
    private function getCurrentMonthApplications(int $userId): int
    {
        return Application::where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Candidature del mese precedente.
     */
    private function getPreviousMonthApplications(int $userId): int
    {
        return Application::where('user_id', $userId)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
    }

    /**
     * Andamento ultime candidature (ultimi 6 mesi).
     */
    private function getLast6MonthsTrend(int $userId): array
    {
        $driver = DB::connection()->getDriverName();
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

        $dateExpression = match ($driver) {
            'pgsql' => "date_trunc('month', created_at)",
            'sqlite' => "strftime('%Y-%m', created_at)",
            'mysql' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => "strftime('%Y-%m', created_at)",
        };

        $rawData = Application::selectRaw("{$dateExpression} as month_label, count(*) as count")
            ->where('user_id', $userId)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupByRaw($dateExpression)
            ->orderByRaw($dateExpression)
            ->get();

        $months = [];
        foreach (range(5, 0) as $i) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $months[$key] = [
                'month' => $date->format('M'),
                'year' => $date->format('Y'),
                'count' => 0,
            ];
        }
        foreach ($rawData as $item) {
            $key = $driver === 'pgsql' ? Carbon::parse($item->month_label)->format('Y-m') : $item->month_label;
            if (isset($months[$key])) {
                $months[$key]['count'] = (int) $item->count;
            }
        }
        return array_values($months);
    }

    /**
     * Distribuzione per stato.
     */
    private function getStatusDistribution(int $userId): array
    {
        return Application::select('status', DB::raw('count(*) as count'))
            ->where('user_id', $userId)
            ->groupBy('status')
            ->get()
            ->toArray();
    }

    /**
     * Ultime 5 candidature.
     */
    private function getLatestApplications(int $userId): Collection
    {
        return Application::with('company')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }
}
