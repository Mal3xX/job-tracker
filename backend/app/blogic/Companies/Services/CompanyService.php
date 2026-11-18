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
     * Recupera le aziende visibili all'account con paginazione.
     * Gli admin vedono tutte le aziende, gli altri solo le proprie.
     *
     * @param Account $account
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(Account $account, int $perPage = 15): LengthAwarePaginator
    {
        $query = Company::orderBy('created_at', 'desc');

        if ($account->role !== 'admin') {
            $query->where('creator_id', $account->user?->id);
        }

        return $query->paginate($perPage);
    }

    /**
     * Restituisce ID e nome delle aziende visibili all'account.
     *
     * @param Account $account
     * @return Collection
     */
    public function getNames(Account $account): Collection
    {
        $query = Company::select('id', 'name')
            ->orderBy('name', 'asc');

        if ($account->role !== 'admin') {
            $query->where('creator_id', $account->user?->id);
        }

        return $query->get();
    }

    /**
     * Recupera una singola azienda visibile all'account.
     * Gli admin possono accedere a qualsiasi azienda, gli altri solo alle proprie.
     *
     * @param int $id
     * @param Account $account
     * @return Company
     */
    public function getById(int $id, Account $account): Company
    {
        $userId = $account->user?->id;
        $query = Company::with('contacts');

        if ($account->role === 'admin') {
            $query->withCount('applications');
        } else {
            $query->withCount([
                'applications' => fn($query) => $query->where('user_id', $userId),
            ]);
            $query->where('creator_id', $userId);
        }

        return $query->findOrFail($id);
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
        $creatorId = $account->user?->id;

        if ($creatorId === null) {
            throw new \RuntimeException('Profilo utente non trovato per l\'account.');
        }

        return DB::transaction(function () use ($data, $creatorId) {
            $company = new Company($data);
            $company->creator_id = $creatorId;
            $company->slug = $this->slugService->generateUnique($data['name'], 'companies', 'slug');
            $company->save();

            $affected = Application::where('user_id', $creatorId)
                ->where('company_name', $company->name)
                ->whereNull('company_id')
                ->update(['company_id' => $company->id]);

            if ($affected > 0) {
                DashboardService::clearStatsCache((int) $creatorId);
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
