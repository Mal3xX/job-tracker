<?php

declare(strict_types=1);

namespace App\blogic\Applications\Policies;

use App\blogic\Accounts\Models\Account;
use App\blogic\Applications\Models\Application;

/**
 * Policy per autorizzare azioni sulle candidature.
 * Gli admin possono gestire tutte le candidature.
 * Gli altri utenti possono gestire solo le proprie candidature.
 */
class ApplicationPolicy
{
    /**
     * Consente agli admin di superare ogni controllo.
     */
    public function before(Account $account, string $ability): ?bool
    {
        return $account->role === 'admin' ? true : null;
    }

    /**
     * Determina se l'account può visualizzare qualsiasi candidatura.
     */
    public function viewAny(Account $account): bool
    {
        return true;
    }

    /**
     * Determina se l'account può visualizzare la candidatura specificata.
     */
    public function view(Account $account, Application $application): bool
    {
        return $account->user?->id === $application->user_id;
    }

    /**
     * Determina se l'account può creare una candidatura.
     */
    public function create(Account $account): bool
    {
        return true;
    }

    /**
     * Determina se l'account può aggiornare la candidatura.
     */
    public function update(Account $account, Application $application): bool
    {
        return $account->user?->id === $application->user_id;
    }

    /**
     * Determina se l'account può eliminare la candidatura.
     */
    public function delete(Account $account, Application $application): bool
    {
        return $account->user?->id === $application->user_id;
    }
}