<?php

declare(strict_types=1);

namespace App\blogic\Applications\Services;

use App\blogic\_Shared\Enums\PlatformType;
use App\blogic\Accounts\Models\Account;
use App\blogic\Applications\Models\Application;
use App\blogic\Dashboard\Services\DashboardService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;

/**
 * Service per la logica di business delle candidature.
 * Gestisce creazione, aggiornamento, eliminazione e cambio stato.
 */
class ApplicationService
{
    /**
     * Verifica se l'account ha ruolo admin.
     */
    private function isAdmin(Account $account): bool
    {
        return $account->role === 'admin';
    }

    /**
     * Recupera tutte le candidature con paginazione.
     * Gli admin vedono tutte le candidature, gli altri utenti solo le proprie.
     *
     * @param Account $account
     * @param int $perPage
     * @param int|null $companyId
     * @return LengthAwarePaginator
     */
    public function getAll(Account $account, int $perPage = 15, ?int $companyId = null): LengthAwarePaginator
    {
        $query = Application::with(['company']);

        if (!$this->isAdmin($account)) {
            $query->where('user_id', $account->user?->id);
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Recupera una singola candidatura per ID.
     * Gli admin possono accedere a qualsiasi candidatura, gli altri solo alle proprie.
     *
     * @param Account $account
     * @param int $id
     * @return Application
     */
    public function getById(Account $account, int $id): Application
    {
        $query = Application::with(['company']);

        if (!$this->isAdmin($account)) {
            $query->where('user_id', $account->user?->id);
        }

        return $query->findOrFail($id);
    }

    /**
     * Crea una nuova candidatura.
     * Imposta lo user_id dall'account e status_changed_at automaticamente.
     *
     * @param array $data
     * @param Account $account
     * @return Application
     */
    public function store(array $data, Account $account): Application
    {
        $userId = $account->user?->id;
        if ($userId === null) {
            throw new RuntimeException('User profile not found for account.');
        }

        $application = new Application($data);
        $application->user_id = $userId;
        $application->status_changed_at = now();
        $application->save();

        DashboardService::clearStatsCache($userId);

        return $application->fresh(['company']);
    }

    /**
     * Aggiorna una candidatura esistente.
     * Se lo stato cambia, aggiorna automaticamente status_changed_at.
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public function update(Application $application, array $data): Application
    {
        if (isset($data['status']) && $data['status'] !== $application->status) {
            $data['status_changed_at'] = now();
        }

        if (isset($data['company_name']) && $application->company_id !== null) {
            if ($application->company?->name !== $data['company_name']) {
                $data['company_id'] = null;
            }
        }

        $application->update($data);

        DashboardService::clearStatsCache($application->user_id);

        return $application->fresh(['company']);
    }

    /**
     * Elimina una candidatura.
     *
     * @param Application $application
     */
    public function delete(Application $application): void
    {
        $userId = $application->user_id;

        $application->delete();

        DashboardService::clearStatsCache($userId);
    }

    /**
     * Restituisce le aziende candidate non ancora collegate.
     *
     * @param Account $account
     * @return Collection
     */
    public function getPendingCompanies(Account $account): Collection
    {
        return Application::where('user_id', $account->user?->id)
            ->whereNotNull('company_name')
            ->whereNull('company_id')
            ->selectRaw('company_name, count(*) as application_count')
            ->groupBy('company_name')
            ->orderByDesc('application_count')
            ->get();
    }

    /**
     * Restituisce l'elenco delle piattaforme distinte usate nelle candidature.
     * Gli admin vedono tutte le piattaforme, gli utenti solo quelle delle proprie candidature.
     *
     * @param Account $account
     * @return Collection
     */
    public function getPlatforms(Account $account): SupportCollection
    {
        $builtIn = collect(PlatformType::cases())->map->value;
        $query = Application::whereNotNull('platform')->where('platform', '!=', '');
        if (!$this->isAdmin($account)) {
            $query->where('user_id', $account->user?->id);
        }
        $fromDb = $query->select('platform')->distinct()->pluck('platform');
        return $builtIn->merge($fromDb)->unique()->sort()->values();
    }
}
