<?php

declare(strict_types=1);

namespace App\blogic\Companies\Services;

use App\blogic\Accounts\Models\Account;
use App\blogic\Applications\Models\Application;
use App\blogic\Companies\Models\Company;
use App\blogic\Dashboard\Services\DashboardService;
use App\blogic\_Shared\Services\SlugService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service per la logica di business delle aziende.
 * Gestisce creazione, aggiornamento, eliminazione e recupero dati.
 */
class CompanyService
{
    public function __construct(
        private SlugService $slugService
    ) {}

    /**
     * Recupera tutte le aziende con paginazione.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Company::with('contacts')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Restituisce ID e nome di tutte le aziende, ordinate alfabeticamente.
     */
    public function getNames(): Collection
    {
        return Company::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Recupera una singola azienda per ID.
     * Lancia eccezione se non trovata.
     *
     * @param int $id
     * @return Company
     */
    public function getById(int $id): Company
    {
        return Company::with('contacts')
            ->withCount('applications')
            ->findOrFail($id);
    }

    /**
     * Crea una nuova azienda con slug automatico.
     *
     * @param array $data
     * @param Account $account
     * @return Company
     */
    public function store(array $data, Account $account): Company
    {
        return DB::transaction(function () use ($data, $account) {
            $company = new Company($data);
            $company->creator_id = $account->user?->id;
            $company->slug = $this->slugService->generateUnique($data['name'], 'companies', 'slug');
            $company->save();

            $affectedUserIds = Application::where('user_id', $account->user?->id)
                ->where('company_name', $company->name)
                ->whereNull('company_id')
                ->pluck('user_id')
                ->unique()
                ->all();

            Application::where('user_id', $account->user?->id)
                ->where('company_name', $company->name)
                ->whereNull('company_id')
                ->update(['company_id' => $company->id]);

            foreach ($affectedUserIds as $userId) {
                DashboardService::clearStatsCache((int) $userId);
            }

            return $company->fresh(['contacts']);
        });
    }

    /**
     * Aggiorna un'azienda esistente.
     *
     * @param Company $company
     * @param array $data
     * @return Company
     */
    public function update(Company $company, array $data): Company
    {
        if (isset($data['name']) && $data['name'] !== $company->name) {
            $data['slug'] = $this->slugService->generateUnique($data['name'], 'companies', 'slug');
        }
        $company->update($data);
        return $company->fresh(['contacts']);
    }

    /**
     * Elimina un'azienda.
     *
     * @param Company $company
     */
    public function delete(Company $company): void
    {
        $company->delete();
    }
}
