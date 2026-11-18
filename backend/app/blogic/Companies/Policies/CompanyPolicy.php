<?php

declare(strict_types=1);

namespace App\blogic\Companies\Policies;

use App\blogic\Accounts\Models\Account;
use App\blogic\Companies\Models\Company;

/**
 * Policy per autorizzare azioni sulle aziende.
 * Gli utenti possono visualizzare solo le proprie aziende;
 * gli admin bypassano i controlli tramite before().
 */
class CompanyPolicy
{
    /**
     * Consente agli admin di superare ogni controllo.
     */
    public function before(Account $account, string $ability): ?bool
    {
        return $account->role === 'admin' ? true : null;
    }

    /**
     * Determina se l'account può visualizzare qualsiasi azienda.
     */
    public function viewAny(Account $account): bool
    {
        return true;
    }

    /**
     * Determina se l'azienda specificata può essere visualizzata dall'account.
     * Solo il creatore può visualizzarla; gli admin sono gestiti da before().
     */
    public function view(Account $account, Company $company): bool
    {
        return $account->user?->id === $company->creator_id;
    }

    /**
     * Determina se l'account può creare un'azienda.
     */
    public function create(Account $account): bool
    {
        return true;
    }

    /**
     * Determina se l'account può aggiornare un'azienda.
     */
    public function update(Account $account, Company $company): bool
    {
        return $account->user?->id === $company->creator_id;
    }

    /**
     * Determina se l'account può eliminare un'azienda.
     */
    public function delete(Account $account, Company $company): bool
    {
        return $account->user?->id === $company->creator_id;
    }
}