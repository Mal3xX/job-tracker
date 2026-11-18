<?php

declare(strict_types=1);

namespace App\blogic\Companies\Policies;

use App\blogic\Accounts\Models\Account;
use App\blogic\Companies\Models\Contact;

/**
 * Policy per autorizzare azioni sui contatti.
 * Gli utenti possono visualizzare solo i propri contatti;
 * gli admin bypassano i controlli tramite before().
 */
class ContactPolicy
{
    /**
     * Consente agli admin di superare ogni controllo.
     */
    public function before(Account $account, string $ability): ?bool
    {
        return $account->role === 'admin' ? true : null;
    }

    /**
     * Determina se l'account può visualizzare qualsiasi contatto.
     */
    public function viewAny(Account $account): bool
    {
        return true;
    }

    /**
     * Determina se il contatto specificato può essere visualizzato dall'account.
     * Solo il creatore può visualizzarlo; gli admin sono gestiti da before().
     */
    public function view(Account $account, Contact $contact): bool
    {
        return $account->user?->id === $contact->creator_id;
    }

    /**
     * Determina se l'account può creare un contatto.
     */
    public function create(Account $account): bool
    {
        return true;
    }

    /**
     * Determina se l'account può aggiornare un contatto.
     */
    public function update(Account $account, Contact $contact): bool
    {
        return $account->user?->id === $contact->creator_id;
    }

    /**
     * Determina se l'account può eliminare un contatto.
     */
    public function delete(Account $account, Contact $contact): bool
    {
        return $account->user?->id === $contact->creator_id;
    }
}
