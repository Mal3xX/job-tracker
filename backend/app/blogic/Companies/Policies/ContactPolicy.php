<?php

declare(strict_types=1);

namespace App\blogic\Companies\Policies;

use App\blogic\Accounts\Models\Account;
use App\blogic\Companies\Models\Contact;

/**
 * Policy per autorizzare azioni sui contatti.
 * Tutti gli utenti autenticati possono visualizzare e creare contatti;
 * solo gli admin possono aggiornarli ed eliminarli.
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
     * Determina se l'account può visualizzare il contatto specificato.
     */
    public function view(Account $account, Contact $contact): bool
    {
        return true;
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
